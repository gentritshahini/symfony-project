<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\News;
use App\Form\CommentType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use HTMLPurifier;
use HTMLPurifier_Config;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(Request $request, PaginatorInterface $paginator, ManagerRegistry $doctrine): Response
    {
        $queryBuilder = $doctrine->getRepository(News::class)->createQueryBuilder('n');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            2
        );

        return $this->render('news/index.html.twig', [
            'news' => $pagination,
        ]);
    }
    #[Route('/news/{id}', name: 'news_show')]
    public function show(News $news, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Sanitize the comment content for security purposes related with xss stored
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $sanitizedContent = $purifier->purify($comment->getContent());
            $comment->setContent($sanitizedContent);

            $comment->setNews($news);
            $comment->setCreatedAt(new \DateTime());
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('news_show', ['id' => $news->getId()]);
        }

        $news->setViews($news->getViews() + 1);
        $entityManager->flush();

        return $this->render('news/show.html.twig', [
            'news' => $news,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/news/top10', name: 'news_top10')]
    public function top10(NewsRepository $newsRepository): Response
    {
        $top10 = $newsRepository->findTop10News();
        return $this->render('news/index.html.twig', [
            'newsList' => $top10,
        ]);
    }
}
