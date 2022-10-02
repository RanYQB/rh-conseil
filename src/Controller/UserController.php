<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'app_user')]
class UserController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(): Response
    {
        return $this->render('user/user.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/en-attente', name: '_pending')]
    public function showStatus(): Response
    {

        return $this->render('user/pending.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
