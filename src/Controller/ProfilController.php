<?php

namespace App\Controller;

use App\Repository\CandidatRepository;
use App\Repository\RecruterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function index(RecruterRepository $recruterRepository, CandidatRepository $candidatRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Check if current user is candidat or recruter
        $currentUser = $this->getUser();

        if (in_array('ROLE_RECRUTER', $currentUser->getRoles())) {
            $user = $recruterRepository->createQueryBuilder('r')
                ->select('r', 'COUNT(j)')
                ->leftJoin('r.jobs', 'j')
                ->where('r = :currentUser')
                ->setParameter('currentUser', $currentUser)
                ->groupBy('r')
                ->getQuery()
                ->getResult();
        } else {
            $user = $candidatRepository->createQueryBuilder('c')
                ->select('c', 'COUNT(a)')
                ->leftJoin('c.applications', 'a')
                ->where('c = :currentUser')
                ->setParameter('currentUser', $currentUser)
                ->groupBy('c')
                ->getQuery()
                ->getResult();
        }

        return $this->render('profil/index.html.twig', [
            'user' => $user,
        ]);
    }
}
