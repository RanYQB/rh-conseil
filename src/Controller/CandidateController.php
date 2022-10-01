<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\CandidateType;
use App\Repository\CandidateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/candidat', name: 'app_candidate')]
class CandidateController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(CandidateRepository $candidateRepository): Response
    {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_CANDIDATE')) {

            $candidate = $candidateRepository->findOneBy(['user' => $user]);

            if (!$candidate) {
                return $this->redirectToRoute('app_candidate_complete_profile');
            }
        }

        return $this->render('candidate/candidate.html.twig', [
            'controller_name' => 'CandidateController',
        ]);
    }



    #[Route('/completer-profil', name: '_complete_profile')]
    public function completeProfile(Request $request , EntityManagerInterface $entityManager, CandidateRepository $candidateRepository): Response
    {

        if ($this->isGranted('ROLE_CANDIDATE')) {
            $user = $this->getUser();

            $candidate = $candidateRepository->findOneBy(['user' => $user]);

            if ($candidate) {
                return $this->redirectToRoute('app_candidate');
            }


            $candidate = new Candidate();
            $form = $this->createForm(CandidateType::class, $candidate);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $candidate->setUser($user);
                $entityManager->persist($candidate);
                $entityManager->flush();
                $this->addFlash('success', 'Votre profil a bien été complété.');
                return $this->redirectToRoute('app_candidate');

            }
        }

        return $this->render('candidate/complete_profile.html.twig', [
            'candidate_form' => $form->createView(),
        ]);
    }
}
