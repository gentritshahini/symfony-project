<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    #[Route('/admin/news', name: 'admin_news_index')]
    public function index(NewsRepository $newsRepository): Response
    {
        $news = $newsRepository->findAll();
        return $this->render('admin/news/index.html.twig', [
            'news' => $news,
        ]);
    }

    #[Route('/admin/news/{id}', name: 'admin_news_show')]
    public function show(News $news): Response
    {
        return $this->render('admin/news/show.html.twig', [
            'news' => $news,
        ]);
    }

    #[Route('/admin/news/new', name: 'admin_news_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news->setCreatedAt(new \DateTime());
            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/news/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/news/{id}/edit', name: 'admin_news_edit')]
    public function edit(News $news, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/news/edit.html.twig', [
            'form' => $form->createView(),
            'news' => $news,
        ]);
    }

    #[Route('/admin/news/{id}/delete', name: 'admin_news_delete')]
    public function delete(News $news, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($news);
        $entityManager->flush();

        return $this->redirectToRoute('admin_news_index');
    }
}
