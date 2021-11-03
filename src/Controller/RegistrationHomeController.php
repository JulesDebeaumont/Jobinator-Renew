<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationCandidatFormType;
use App\Form\RegistrationRecruterFormType;
use App\Security\LoginFormAnthenticatorAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

/**
 * @Route("/register")
 */
class RegistrationHomeController extends AbstractController
{

    /**
     * @Route("", name="app_register")
     */
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/index.html.twig');
    }


    /**
     * @Route("/candidat", name="app_register_candidat")
     */
    public function registerCandidat(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        LoginFormAnthenticatorAuthenticator $authenticator,
        UserAuthenticatorInterface $userAuthenticator
    ): Response {

        // auto redirect if already logged in
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationCandidatFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            // auto log in
            return $userAuthenticator->authenticateUser($user, $authenticator, $request);
        }

        return $this->render('registration/registerCandidat.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/recruter", name="app_register_recruter")
     */
    public function registerRecruter(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        LoginFormAnthenticatorAuthenticator $authenticator,
        UserAuthenticatorInterface $userAuthenticator
    ): Response {

        // auto redirect if already logged in
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationRecruterFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            // auto log in
            return $userAuthenticator->authenticateUser($user, $authenticator, $request);
        }

        return $this->render('registration/registerRecruter.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
