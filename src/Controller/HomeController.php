<?php

namespace App\Controller;

use App\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        $jobWhat = $request->query->get('job-what');
        $jobWhere = $request->query->get('job-where');

        if ($jobWhat || $jobWhere) {
            // redirects to a route and maintains the original query string parameters
            return $this->redirectToRoute('search', $request->query->all());
        }

        return $this->render('home/index.html.twig', [
            'request' => $request
        ]);

    }

    /**
     * @Route("/home", name="home-page")
     */
    public function home(): Response
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, PaginatorInterface $paginator): Response
    {
        $jobRepository = $this->getDoctrine()->getRepository(Job::class);

        $jobWhat = $request->query->get('job-what');
        $jobWhere = $request->query->get('job-where');

        $query = $jobRepository->createQueryBuilder('j')
            ->select('j', 'c', 't')
            ->leftJoin('j.category', 'c')
            ->leftJoin('j.type', 't')
            ->where("
            (j.name LIKE :jobWhat OR j.company LIKE :jobWhat) 
            AND
            (j.departement LIKE :jobWhere OR j.location LIKE :jobWhere OR j.departement IS NULL OR j.location IS NULL)")
            ->setParameter('jobWhat', "%{$jobWhat}%")
            ->setParameter('jobWhere', "%{$jobWhere}%")
            ->orderBy('j.updatedAt')
            ->getQuery();


        $jobs = $paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('home/search.html.twig', [
            'jobs' => $jobs,
            'jobWhere' => $jobWhere,
            'jobWhat' => $jobWhat
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
