<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(NewsRepository $newsRepository): Response
    {
        $news = $newsRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('news/index.html.twig', [
            'newsList' => $news,
        ]);
    }
    #[Route('/news/{id}', name: 'news_show')]
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
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
