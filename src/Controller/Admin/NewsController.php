<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/news', name: "admin_news_")]
class NewsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, PaginatorInterface $paginator, ManagerRegistry $doctrine): Response
    {
        $queryBuilder = $doctrine->getRepository(News::class)->createQueryBuilder('n');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/news/index.html.twig', [
            'news' => $pagination,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $newFilename = uniqid() . '.' . $pictureFile->guessExtension();
                $pictureFile->move(
                    $this->getParameter('uploads'),
                    $newFilename
                );
                $news->setPicture($newFilename);
            }

            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('admin_news_index');
        }

        $categories = $categoryRepository->findAll();

        return $this->render('admin/news/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(News $news, EntityManagerInterface $entityManager): Response
    {
        $news->setViews($news->getViews() + 1);
        $entityManager->flush();

        return $this->render('admin/news/show.html.twig', [
            'news' => $news,
        ]);
    }


    #[Route('/{id}/edit', name: 'edit')]
    public function edit(News $news, Request $request, EntityManagerInterface $entityManager): Response
    {
        $originalPicture = $news->getPicture();

        $form = $this->createForm(NewsType::class, $news, [
            'is_edit' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile instanceof File) {
                $newFilename = uniqid() . '.' . $pictureFile->guessExtension();
                $pictureFile->move(
                    $this->getParameter('uploads'),
                    $newFilename
                );
                $news->setPicture($newFilename);
                if ($originalPicture && file_exists($this->getParameter('uploads') . '/' . $originalPicture)) {
                    unlink($this->getParameter('uploads') . '/' . $originalPicture);
                }
            } else {
                $news->setPicture($originalPicture);
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/news/edit.html.twig', [
            'form' => $form->createView(),
            'news' => $news,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(News $news, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($news);
        $entityManager->flush();

        return $this->redirectToRoute('admin_news_index');
    }
}
