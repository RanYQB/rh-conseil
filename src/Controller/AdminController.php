<?php

namespace App\Controller;

use App\Entity\Consultant;
use App\Entity\User;
use App\Form\ConsultantType;
use App\Form\ConsultantUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }


    #[Route('/nouveau-consultant', name: '_add_consultant')]
    public function addConsultant(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if($this->isGranted('ROLE_ADMIN')){

            $user = new User();
            $consultant = new Consultant();
            $items = ['user' => $user, 'consultant' => $consultant];

            $form = $this->createFormBuilder($items)
                ->add('user', ConsultantUserType::class)
                ->add('consultant', ConsultantType::class)
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $user->setRoles((array)'ROLE_CONSULTANT');
                $user->setActive(true);
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('user')->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $consultant->setUser($user);
                $entityManager->persist($consultant);

                $entityManager->flush();

                $email = (new TemplatedEmail())
                    ->from(new Address('admin@rhconseil.test', 'RH Conseil'))
                    ->to($consultant->getUser()->getEmail())
                    ->subject('Bienvenue chez RH Conseil')
                    ->htmlTemplate('email/new_consultant.html.twig')
                    ->context([
                        'user' => $user,
                        'consultant' => $consultant,
                        'password' => $form->get('user')->get('plainPassword')->getData(),
                    ]);
                $mailer->send($email);
                $this->addFlash('success', 'Le consultant a bien été créé. ');
                return $this->redirectToRoute('app_admin');
            }
        }

        return $this->render('admin/add_consultant.html.twig', [
            'consultant_form' => $form->createView(),
        ]);
    }
}
