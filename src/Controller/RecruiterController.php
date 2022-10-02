<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Recruiter;
use App\Form\OfferType;
use App\Form\RecruiterType;
use App\Repository\RecruiterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/recruteur', name: 'app_recruiter')]
class RecruiterController extends AbstractController
{

    public function __construct(private SluggerInterface $slugger)
    {
    }


    #[Route('/', name: '')]
    public function index(RecruiterRepository $recruiterRepository): Response
    {
        $user = $this->getUser();
        // $recruiter = $recruiterRepository->findOneBy(['user' => $user]);
        if ($this->isGranted('ROLE_RECRUITER')) {

            $recruiter = $recruiterRepository->findOneBy(['user' => $user]);

            if (!$recruiter) {
                return $this->redirectToRoute('app_recruiter_complete_profile');
            }

            if($recruiter->getUser()->isActive() == false){
                return $this->redirectToRoute('app_user_pending');
            }
        }

        return $this->render('recruiter/recruiter.html.twig', [
            'controller_name' => 'RecruiterController',
        ]);
    }


    #[Route('/nouvelle-offre', name: '_add_offer')]
    public function addOffer(RecruiterRepository $recruiterRepository, Request $request, EntityManagerInterface $entityManager,): Response
    {
        $user = $this->getUser();
        if ($this->isGranted('ROLE_RECRUITER')) {

            $recruiter = $recruiterRepository->findOneBy(['user' => $user]);

            if (!$recruiter) {
                return $this->redirectToRoute('app_recruiter_complete_profile');
            }

            $offer = new Offer();
            $form = $this->createForm(OfferType::class, $offer);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $offer->setRecruiter($recruiter);
                $offer->setPublished(false);
                $offer->setClosed(false);
                $offer->setSlug($this->slugger->slug($offer->getTitle())->lower());
                $city = $form->get('city')->getData();
                $offer->setCity($city);
                $entityManager->persist($offer);
                $entityManager->flush();
                $this->addFlash('success', 'Votre offre a bien été enregistrée.');
                return $this->redirectToRoute('app_recruiter');

            }

        }
        return $this->render('recruiter/add_offer.html.twig', [
            'offer_form' => $form->createView(),
        ]);
    }

    #[Route('/completer-profil', name: '_complete_profile')]
    public function completeProfile(Request $request , EntityManagerInterface $entityManager, RecruiterRepository $recruiterRepository): Response
    {

        if ($this->isGranted('ROLE_RECRUITER')) {
            $user = $this->getUser();

            $recruiter = $recruiterRepository->findOneBy(['user' => $user]);

            if ($recruiter) {
                return $this->redirectToRoute('app_recruiter');
            }


            $recruiter = new Recruiter();
            $form = $this->createForm(RecruiterType::class, $recruiter);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $recruiter->setUser($user);
                $city = $form->get('city')->getData();
                $recruiter->setCity($city);
                $entityManager->persist($recruiter);
                $entityManager->flush();
                $this->addFlash('success', 'Votre profil a bien été complété.');
                return $this->redirectToRoute('app_recruiter');

            }
        }

        return $this->render('recruiter/complete_profile.html.twig', [
            'recruiter_form' => $form->createView(),
        ]);
    }
}
