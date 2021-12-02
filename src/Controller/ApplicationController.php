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
        $this->denyAccessUnlessGranted('JOB_APPLY', $job);

        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * Gestion des images
             */
            $images = $form->get('files')->getData();

            foreach ($images as $image) {
                // génère un nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // Copie du fichier dans le dossier
                $image->move(
                    // On va chercher la route du dossier dans le services.yaml
                    $this->getParameter('application_file_directory'),
                    $fichier
                );

                // On stock le nom du fichier dans la BD
                $img = new FileApplication();
                $img->setName($fichier);
                // $img->setApplication($application);
                $application->addFile($img);
            }
            $application->setJob($job);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($application);
            $entityManager->flush();

            return $this->redirectToRoute('my_applications', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('application/new.html.twig', [
            'application' => $application,
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'application_show', methods: ['GET'])]
    public function show(Application $application, Job $job): Response
    {
        $this->denyAccessUnlessGranted('APPLICATION_READ');

        return $this->render('application/show.html.twig', [
            'application' => $application,
            'job' => $job
        ]);
    }
}
