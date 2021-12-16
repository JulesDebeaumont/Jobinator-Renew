<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\JobImage;
use App\Form\JobType;
use App\Repository\CandidatRepository;
use App\Service\FileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job')]
class JobController extends AbstractController
{

    #[Route('/new', name: 'job_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileManager $fileManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTER');

        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('companyImage')->getData();

            if ($file) {
                $newFile = $fileManager->uploadImage($file);

                $jobImage = new JobImage();
                $jobImage->setName($newFile);
                $job->setJobImage($jobImage);
            }

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

    #[Route('/{slug}', name: 'job_show', methods: ['GET'])]
    public function show(Job $job): Response
    {
        return $this->render('job/show.html.twig', [
            'job' => $job
        ]);
    }

    #[Route('/{slug}/edit', name: 'job_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{slug}', name: 'job_delete', methods: ['POST'])]
    public function delete(Request $request, Job $job, FileManager $fileManager): Response
    {
        $this->denyAccessUnlessGranted('JOB_DELETE', $job);

        if ($this->isCsrfTokenValid('delete' . $job->getId(), $request->request->get('_token'))) {

            // Suppression des fichiers des candidatures
            $fileManager->deleteAllJobRelatedFiles($job);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($job);
            $entityManager->flush();
        }

        return $this->redirectToRoute('my_jobs', [], Response::HTTP_SEE_OTHER);
    }
}
