<?php

namespace App\Controller;

use App\Form\CandidatType;
use App\Form\RecruterType;
use App\Repository\CandidatRepository;
use App\Repository\RecruterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{

    private function isCandidat(): bool
    {
        return in_array('ROLE_CANDIDAT', $this->getUser()->getRoles());
    }

    private function isRecruter(): bool
    {
        return in_array('ROLE_RECRUTER', $this->getUser()->getRoles());
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

        return $this->redirectToRoute('about'); // TODO make an error controller
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

        return $this->redirectToRoute('about'); // TODO make an error controller
    }
}
