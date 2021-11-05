<?php

namespace App\Controller;

use App\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        if ($request->query->get('job-where') || $request->query->get('job-what')) {
            return $this->redirectToRoute('search');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request): Response
    {
        $jobRepository = $this->getDoctrine()->getRepository(Job::class);

        // Choppe la valeur de l'input avec la props name="job-what"
        $jobWhat = $request->query->get('job-what');
        $jobWhere = $request->query->get('job-where');

        $query = $jobRepository->createQueryBuilder('j')
            ->select('j', 'c', 't')
            ->leftJoin('j.category', 'c')
            ->leftJoin('j.type', 't')
            ->leftJoin('j.recruter', 'r')
            ->where('
            (j.name LIKE :jobWhat OR j.name LIKE r.company) 
            AND 
            (j.departement LIKE :jobWhere OR j.location LIKE :jobWhere)')
            ->setParameter('jobWhat', "%{$jobWhat}%")
            ->setParameter('jobWhere', "%{$jobWhere}%")
            ->orderBy('j.updatedAt')
            ->getQuery();

        $jobs = $query->getResult();

        return $this->render('home/search.html.twig', [
            'jobs' => $jobs
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }
}
