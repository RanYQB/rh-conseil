<?php

namespace App\Controller;

use App\Repository\ApplicationRepository;
use App\Repository\CandidateRepository;
use App\Repository\OfferRepository;
use App\Repository\RecruiterRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/consultant', name: 'app_consultant')]
class ConsultantController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(): Response
    {
        return $this->render('consultant/consultant.html.twig', [
            'controller_name' => 'ConsultantController',
        ]);
    }

    #[Route('/candidats-en-attente', name: '_pending_candidates')]
    public function showPendingCandidates(CandidateRepository $candidateRepository): Response
    {
        if ($this->isGranted('ROLE_CONSULTANT')) {

            $candidates = $candidateRepository->showPending();
        }

        return $this->render('consultant/pending_candidates.html.twig', [
            'candidates' => $candidates,
        ]);
    }

    #[Route('/recruteurs-en-attente', name: '_pending_recruiters')]
    public function showPendingRecruiters(RecruiterRepository $recruiterRepository): Response
    {
        if ($this->isGranted('ROLE_CONSULTANT')) {

            $recruiters = $recruiterRepository->showPending();
        }

        return $this->render('consultant/pending_recruiters.html.twig', [
            'recruiters' => $recruiters,
        ]);
    }

    #[Route('/validation-du-profil/{id}', name: '_validate_profile')]
    public function validateProfile(UserRepository $userRepository, int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_CONSULTANT')) {

            $user = $userRepository->findOneBy(['id' => $id]);
            $user->setActive(true);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }

    #[Route('/offres-en-attente', name: '_pending_offers')]
    public function viewUnpublishedOffers(OfferRepository $offerRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_CONSULTANT')) {
            $offers = $offerRepository->showUnpublished();
        }

        return $this->render('consultant/pending_offers.html.twig', [
            'offers' => $offers,
        ]);
    }

    #[Route('/publier-offre/{id}', name: '_validate_offer')]
    public function validateOffer(int $id, OfferRepository $offerRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_CONSULTANT')) {

            $offer = $offerRepository->find($id);
            $offer->setPublished(true);
            $offer->setPublishedAt(new \DateTimeImmutable());
            $entityManager->persist($offer);
            $entityManager->flush();
        }

        $this->addFlash('success', 'L\'offre d\'emploi a bien été publiée.');
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }




    #[Route('/candidatures-en-attente', name: '_pending_applications')]
    public function viewPendingApplications(ApplicationRepository $applicationRepository): Response
    {
        if ($this->isGranted('ROLE_CONSULTANT')) {

            $applications = $applicationRepository->showUnsent();
        }

        return $this->render('consultant/pending_APPLICATIONS.html.twig', [
            'applications' => $applications,
        ]);
    }



    #[Route('/envoyer-candidature/{id}', name: '_validate_application')]
    public function validateApplication(int $id, MailerInterface $mailer,
         ApplicationRepository $applicationRepository,
         Request $request,
         EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_CONSULTANT')) {

            $application = $applicationRepository->find($id);
            $application->setSent(true);
            $application->setSentAt(new \DateTimeImmutable());
            $entityManager->persist($application);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('consultant1@rhconseil.test',
                    'RH Conseil'))
                ->to($application->getCandidate()->getUser()->getEmail())
                ->subject('Votre candidature au poste de '
                    . $application->getOffer()->getTitle())
                ->htmlTemplate('email/application_sent_email.html.twig')
                ->context([
                    'candidate'=> $application->getCandidate(),
                    'offer' => $application->getOffer(),
                ]);
            $mailer->send($email);

            $applicationEmail = (new TemplatedEmail())
                ->from(new Address('consultant1@rhconseil.test',
                    'RH Conseil'))
                ->to($application->getOffer()->getRecruiter()->getUser()->getEmail())
                ->attachFromPath($application->getCandidate()->getResume()->getPath(),
                    'CV-'.$application->getCandidate()->getLastname())
                ->subject('Vous avez reçu une nouvelle candidature pour l\'offre : '
                    . $application->getOffer()->getTitle())
                ->htmlTemplate('email/application_email.html.twig')
                ->context([
                    'candidate'=> $application->getCandidate(),
                    'offer' => $application->getOffer(),
                ]);
            $mailer->send($applicationEmail);

        }

        $this->addFlash('success', 'La candidature a bien été transmise à l\'entreprise.');

        $route = $request->headers->get('referer');
        return $this->redirect($route);
    }

    #[Route('/refuser-candidature/{id}', name: '_decline_application')]
    public function declineApplication(int $id, Request $request, ApplicationRepository $applicationRepository, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if ($this->isGranted('ROLE_CONSULTANT')) {

            $application = $applicationRepository->find($id);
            $application->setSent(false);
            $application->setApproved(false);
            $entityManager->persist($application);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('consultant1@rhconseil.test', 'RH Conseil'))
                ->to($application->getCandidate()->getUser()->getEmail())
                ->subject('Votre candidature au poste de ' . $application->getOffer()->getTitle())
                ->htmlTemplate('email/application_decline_email.html.twig')
                ->context([
                    'candidate'=> $application->getCandidate(),
                    'offer' => $application->getOffer(),
                ]);
            $mailer->send($email);


        }

        $this->addFlash('success', 'La candidature a bien été rejetée.');
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }


}
