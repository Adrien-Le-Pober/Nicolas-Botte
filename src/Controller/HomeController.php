<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\AtelierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        AtelierRepository $atelierRepository,
        ArticleRepository $articleRepository
    ): Response
    {
        return $this->render('home/index.html.twig', [
            'lastAteliers' => $atelierRepository->findBy([], ['date' => 'DESC'], 3),
            'lastArticles' => $articleRepository->findBy([], ['createdAt' => 'DESC'], 3),
        ]);
    }

    #[Route('/naturopathie', name: 'app_naturopathie')]
    public function naturopathie(): Response
    {
        return $this->render('home/naturopathie.html.twig');
    }

    #[Route('/hypnose', name: 'app_hypnose')]
    public function hypnose(): Response
    {
        return $this->render('home/hypnose.html.twig');
    }

    #[Route('/qui-suis-je', name: 'app_qui-suis-je')]
    public function quiSuisJe(): Response
    {
        return $this->render('home/qui-suis-je.html.twig');
    }

    #[Route('/rendez-vous', name: 'app_rendez-vous')]
    public function rendezVous(): Response
    {
        return $this->render('home/rendez-vous.html.twig');
    }
}
