<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Recruter;
use App\Entity\User;
use App\Form\CandidatType;
use App\Form\RecruterType;
use App\Repository\ApplicationRepository;
use App\Repository\CandidatRepository;
use App\Repository\JobRepository;
use App\Repository\RecruterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

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
    public function candidatApplications(ApplicationRepository $applicationRepository): Response
    {
        if ($this->isCandidat()) {
            $applications = $applicationRepository->createQueryBuilder('a')
                ->select('a', 'j')
                ->leftJoin('a.candidat', 'c')
                ->leftJoin('a.job', 'j')
                ->where('c.id = :currentUser')
                ->setParameter('currentUser', $this->getUser()->getId())
                ->orderBy('a.createdAt', 'DESC')
                ->getQuery()
                ->getResult();

            return $this->render('profil/candidat/applications.html.twig', [
                'applications' => $applications,
            ]);
        }

        return $this->redirect('home');
    }


    /**
     * @Route("/my-jobs", name="my_jobs")
     */
    public function recruterJobs(JobRepository $jobRepository): Response
    {
        if ($this->isRecruter()) {
            $jobs = $jobRepository->createQueryBuilder('j')
                ->select('j', 'a', 'COUNT(c)')
                ->leftJoin('j.recruter', 'r')
                ->leftJoin('j.applications', 'a')
                ->leftJoin('a.candidat', 'c')
                ->where('r.id = :currentUser')
                ->setParameter('currentUser', $this->getUser()->getId())
                ->groupBy('a')
                ->orderBy('j.updatedAt', 'DESC')
                ->getQuery()
                ->getResult();

            return $this->render('profil/recruter/jobs.html.twig', [
                'jobs' => $jobs
            ]);
        }

        return $this->redirect('home');
    }

    /**
     * @Route("/account-delete", name="account_delete", methods={"POST"})
     */
    public function deleteAccount(Request $request): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->redirectToRoute('home');
        }

        $currentUser = $this->getUser();

        // Suppression des fichiers des candidatures
        if ($currentUser instanceof Candidat) {
            foreach ($currentUser->getApplications() as $application) {
                foreach ($application->getFiles() as $file) {
                    $fileName = $this->getParameter('application_file_directory') . '/' . $file->getName();

                    if (file_exists($fileName)) {
                        unlink($fileName);
                    }
                }
            }
        } else if ($currentUser instanceof Recruter) {
            // handle delete images
        }

        if ($this->isCsrfTokenValid('delete' . $currentUser->getId(), $request->request->get('_token'))) {
            // Enlève la session courante
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($currentUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home');
    }
}
