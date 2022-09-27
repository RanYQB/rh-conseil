<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecruiterController extends AbstractController
{
    #[Route('/recruiter', name: 'app_recruiter')]
    public function index(): Response
    {
        return $this->render('recruiter/admin.html.twig', [
            'controller_name' => 'RecruiterController',
        ]);
    }
}
