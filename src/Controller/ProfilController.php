<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Form\CandidatType;
use App\Form\RecruterType;
use App\Repository\ApplicationRepository;
use App\Repository\CandidatRepository;
use App\Repository\JobRepository;
use App\Repository\RecruterRepository;
use App\Service\FileManager;
use App\Service\MailSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class ProfilController extends AbstractController
{
    private function isCandidat(): bool
    {
        return $this->getUser() ? in_array('ROLE_CANDIDAT', $this->getUser()->getRoles()) : false;
    }

    private function isRecruter(): bool
    {
        return $this->getUser() ? in_array('ROLE_RECRUTER', $this->getUser()->getRoles()) : false;
    }

    /**
     * @Route("/profil", name="profil")
     */
    public function index(RecruterRepository $recruterRepository, CandidatRepository $candidatRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $currentUser = $this->getUser();

        // Check if current user is candidat or recruter
        if ($this->isRecruter()) {
            $user = $recruterRepository->createQueryBuilder('r')
                ->select('r', 'COUNT(j)')
                ->leftJoin('r.jobs', 'j')
                ->where('r = :currentUser')
                ->setParameter('currentUser', $currentUser)
                ->groupBy('r')
                ->getQuery()
                ->getResult();

            return $this->render('profil/recruter/recruter.html.twig', [
                'user' => $user,
            ]);
        } else if ($this->isCandidat()) {
            $user = $candidatRepository->createQueryBuilder('c')
                ->select('c', 'COUNT(a)')
                ->leftJoin('c.applications', 'a')
                ->where('c.id = :currentUser')
                ->setParameter('currentUser', $currentUser)
                ->groupBy('c')
                ->getQuery()
                ->getResult();

            return $this->render('profil/candidat/candidat.html.twig', [
                'user' => $user,
            ]);
        }

        return $this->redirectToRoute('about');
    }


    /**
     * @Route("/profil/edit", name="proflil_edit", methods={"GET","POST"})
     */
    public function edit(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $currentUser = $this->getUser();

        // Recruter
        if ($this->isRecruter()) {
            $form = $this->createForm(RecruterType::class, $currentUser);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('profil', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('profil/recruter/edit.html.twig', [
                'recruter' => $currentUser,
                'form' => $form
            ]);

            // Candidat
        } else if ($this->isCandidat()) {
            $form = $this->createForm(CandidatType::class, $currentUser);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('profil', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('profil/candidat/edit.html.twig', [
                'candidat' => $currentUser,
                'form' => $form
            ]);
        }

        return $this->redirectToRoute('about');
    }

    /**
     * @Route("/my-applications", name="my_applications")
     */
    public function candidatApplications(ApplicationRepository $applicationRepository, Request $request, PaginatorInterface $paginator): Response
    {
        if ($this->isCandidat()) {
            $query = $applicationRepository->createQueryBuilder('a')
                ->select('a', 'j')
                ->leftJoin('a.candidat', 'c')
                ->leftJoin('a.job', 'j')
                ->where('c.id = :currentUser')
                ->setParameter('currentUser', $this->getUser()->getId())
                ->orderBy('a.createdAt', 'DESC')
                ->getQuery();

                $applications = $paginator->paginate($query, $request->query->getInt('page', 1), 5);

            return $this->render('profil/candidat/applications.html.twig', [
                'applications' => $applications,
            ]);
        }

        return $this->redirect('home');
    }


    /**
     * @Route("/my-jobs", name="my_jobs")
     */
    public function recruterJobs(JobRepository $jobRepository, Request $request, PaginatorInterface $paginator): Response
    {
        if ($this->isRecruter()) {
            $query = $jobRepository->createQueryBuilder('j')
                ->select('j', 'a', 'COUNT(c)')
                ->leftJoin('j.recruter', 'r')
                ->leftJoin('j.applications', 'a')
                ->leftJoin('a.candidat', 'c')
                ->where('r.id = :currentUser')
                ->setParameter('currentUser', $this->getUser()->getId())
                ->groupBy('a')
                ->orderBy('j.updatedAt', 'DESC')
                ->getQuery();

            $jobs = $paginator->paginate($query, $request->query->getInt('page', 1), 5);

            return $this->render('profil/recruter/jobs.html.twig', [
                'jobs' => $jobs
            ]);
        }

        return $this->redirect('home');
    }

    /**
     * @Route("/account-delete", name="account_delete", methods={"POST"})
     */
    public function deleteAccount(Request $request, FileManager $fileManager, MailSender $mailSender): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->redirectToRoute('home');
        }

        $currentUser = $this->getUser();

        // Suppression des fichiers lié à l'utilisateur
        if ($currentUser instanceof Candidat) {
            $fileManager->deleteAllCandidatFiles($currentUser);
        }

        if ($this->isCsrfTokenValid('delete' . $currentUser->getId(), $request->request->get('_token'))) {

            // Store le mail avant la fin de session et le remove de la BD
            $currentUserEmail = $currentUser->getEmail();

            // Enlève la session courante
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($currentUser);
            $entityManager->flush();

            $mailSender->deleteAccountMail($currentUserEmail);
        }

        return $this->redirectToRoute('home');
    }
}
