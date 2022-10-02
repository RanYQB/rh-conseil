<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Resume;
use App\Form\CandidateType;
use App\Form\ResumeType;
use App\Repository\CandidateRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function completeProfile(Request $request , FileUploader $fileUploader, EntityManagerInterface $entityManager, CandidateRepository $candidateRepository): Response
    {

        if ($this->isGranted('ROLE_CANDIDATE')) {
            $user = $this->getUser();

            $candidate = $candidateRepository->findOneBy(['user' => $user]);

            if ($candidate) {
                return $this->redirectToRoute('app_candidate');
            }


            $candidate = new Candidate();
            $resume = new Resume();
            $items = ['candidate' => $candidate, 'resume' => $resume];
            $form = $this->createFormBuilder($items)
                ->add('candidate', CandidateType::class)
                ->add('resume', ResumeType::class)
                ->getForm();
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                /** @var UploadedFile $resumeFileFile */
                $resumeFile = $form->get('resume')["name"]->getData();

                if ($resumeFile) {
                    $resumeFileName = $fileUploader->upload($resumeFile);
                    $resume->setName($resumeFileName);
                    $resume->setPath('uploads/resumes/' . $resumeFileName);

                }

                $candidate->setUser($user);
                $candidate->setResume($resume);
                $entityManager->persist($candidate);
                $resume->setCandidate($candidate);
                $entityManager->persist($resume);
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
