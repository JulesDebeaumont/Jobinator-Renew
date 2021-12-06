<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\FileApplication;
use App\Entity\Job;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

#[Route('/job/{job_id}/application')]
#[ParamConverter("job", class: Job::class, options: ["id" => "job_id"])]
class ApplicationController extends AbstractController
{
    #[Route('/', name: 'application_index', methods: ['GET'])]
    public function index(ApplicationRepository $applicationRepository, Job $job): Response
    {
        $this->denyAccessUnlessGranted('JOB_EDIT', $job);

        $applications = $applicationRepository->createQueryBuilder('a')
            ->leftJoin('a.job', 'j')
            ->where('j.id = :jobId')
            ->setParameter('jobId', $job->getId())
            ->getQuery()
            ->getResult();

        return $this->render('application/index.html.twig', [
            'applications' => $applications,
            'job' => $job
        ]);
    }

    #[Route('/new', name: 'application_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Job $job): Response
    {
        if (!$this->isGranted('JOB_APPLY', $job)) {
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * Gestion des fichiers
             * https://symfony.com/doc/current/controller/upload_file.html
             */
            $files = $form->get('files')->getData();

            foreach ($files as $file) {
                // génère un nom de fichier
                $newFile = md5(uniqid()) . '.' . $file->guessExtension();

                // Copie du fichier dans le dossier
                try {
                    $file->move(
                        // On va chercher la route du dossier dans le services.yaml
                        $this->getParameter('application_file_directory'),
                        $newFile
                    );
                } catch (FileException $error) {
                    throw new FileException('An error has occured while uploadign the file' . $file . '\n' . $error->getMessage());
                }

                // On stock le nom du fichier dans la BD
                $fileApplication = new FileApplication();
                $fileApplication->setName($file);
                $application->addFile($fileApplication);
            }

            $application->setJob($job);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($application);
            $entityManager->flush();

            return $this->redirectToRoute('apply_success', ['job_id' => $job->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('application/new.html.twig', [
            'application' => $application,
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/success', name: 'apply_success', methods: ['GET'])]
    public function success(Job $job): Response
    {
        if ($this->isGranted('JOB_APPLY', $job) || $this->isGranted('ROLE_RECRUTER')) {
            return $this->redirectToRoute('job_show', ['id' => $job->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job/success.html.twig', [
            'job' => $job
        ]);
    }

    #[Route('/{id}', name: 'application_show', methods: ['GET'])]
    public function show(Job $job, Application $application): Response
    {
        $this->denyAccessUnlessGranted('JOB_EDIT', $job);

        return $this->render('application/show.html.twig', [
            'application' => $application,
            'job' => $job
        ]);
    }

    #[Route('/{id}/file/{file_id}', name: 'application_file', methods: ['GET'])]
    #[ParamConverter("file", class: FileApplication::class, options: ["id" => "file_id"])]
    public function download(Job $job, Application $application, FileApplication $file): Response
    {
        $this->denyAccessUnlessGranted('JOB_EDIT', $job);

        return $this->file($this->getParameter('application_file_directory') . DIRECTORY_SEPARATOR . $file->getName(), $file->getName(), ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
