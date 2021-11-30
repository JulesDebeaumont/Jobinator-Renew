<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\CandidatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job')]
class JobController extends AbstractController
{
    private function isCandidat(): bool
    {
        return $this->getUser() ? in_array('ROLE_CANDIDAT', $this->getUser()->getRoles()) : false;
    }

    #[Route('/new', name: 'job_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTER');

        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($job);
            $entityManager->flush();

            return $this->redirectToRoute('my_jobs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job/new.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'job_show', methods: ['GET'])]
    public function show(CandidatRepository $candidatRepository, Job $job): Response
    {
        $alreadyApplied = false;
        if ($this->isCandidat()) {
            $results = $candidatRepository->createQueryBuilder('c')
                ->leftJoin('c.applications', 'a')
                ->where('a.job = :currentJob')
                ->setParameter('currentJob', $job)
                ->getQuery()
                ->getResult();

            if ($results) {
                $alreadyApplied = true;
            }
        }

        return $this->render('job/show.html.twig', [
            'job' => $job,
            'alreadyApplied' => $alreadyApplied
        ]);
    }

    #[Route('/{id}/edit', name: 'job_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Job $job): Response
    {
        $this->denyAccessUnlessGranted('JOB_EDIT', $job);

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('my_jobs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job/edit.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'job_delete', methods: ['POST'])]
    public function delete(Request $request, Job $job): Response
    {
        $this->denyAccessUnlessGranted('JOB_DELETE', $job);

        if ($this->isCsrfTokenValid('delete' . $job->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($job);
            $entityManager->flush();
        }

        return $this->redirectToRoute('my_jobs', [], Response::HTTP_SEE_OTHER);
    }
}
