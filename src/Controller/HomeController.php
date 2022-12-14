<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger){}

    #[Route('/', name: 'app_home')]
    public function index( Request $request): Response
    {
        $keywordLabel = $request->get('keyword');
        $city = $request->get('city');

        if($keywordLabel != "" && $keywordLabel != null && $city != "" && $city != null  ){
            $keyword = $this->slugger->slug($keywordLabel)->lower();
            return new JsonResponse([
                'content' => $this->renderView('partials/_homeContent.html.twig', [
                    'keyword' => $keyword,
                    'city' => $city,
                ])
            ]);
        }

        return $this->render('home/home.html.twig', [
        ]);
    }
}

