<?php

namespace App\Controller;

use App\Form\MainSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $form = $this->createForm(MainSearchType::class);

        return $this->render('home/home.html.twig', [
            'search_form' => $form->createView(),
        ]);
    }
}
