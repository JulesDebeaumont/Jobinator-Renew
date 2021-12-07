<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Recruter;
use App\Form\RegistrationCandidatFormType;
use App\Form\RegistrationRecruterFormType;
use App\Security\LoginFormAnthenticatorAuthenticator;
use App\Service\MailSender;
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

    private const roleCandidat = 'ROLE_CANDIDAT';
    private const roleRecruter = 'ROLE_RECRUTER';


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
        UserAuthenticatorInterface $userAuthenticator,
        MailSender $mailSender
    ): Response {

        // auto redirect if already logged in
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user = new Candidat();
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

            $user->setRoles(array_merge($user->getRoles(), [self::roleCandidat]));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $mailSender->registrationMail($user->getEmail(), true);

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
        UserAuthenticatorInterface $userAuthenticator,
        MailSender $mailSender
    ): Response {

        // auto redirect if already logged in
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user = new Recruter();
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
            $user->setRoles(array_merge($user->getRoles(), [self::roleRecruter]));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $mailSender->registrationMail($user->getEmail(), false);

            // auto log in
            return $userAuthenticator->authenticateUser($user, $authenticator, $request);
        }

        return $this->render('registration/registerRecruter.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
