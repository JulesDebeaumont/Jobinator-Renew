<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/message')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'messages', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('conversation_index');
    }

    #[Route('/conversation', name: 'conversation_index', methods: ['GET'])]
    public function sent(Request $request, MessageRepository $messageRepository, PaginatorInterface $paginator): Response
    {
        $conversationQuery = $messageRepository->createQueryBuilder('m')
            ->where('m.receiver = :currentUser')
            ->leftJoin('m.application', 'a')
            ->setParameter('currentUser', $this->getUser())
            ->orderBy('m.createdAt', 'DESC')
            ->groupBy('a.job')
            ->getQuery();

        $conversations = $paginator->paginate($conversationQuery, $request->query->getInt('page', 1), 10);

        return $this->render('message/conversation_index.html.twig', [
            'messages' => $conversations
        ]);
    }

    #[Route('/conversation/application/{slug}', name: 'message_new', methods: ['GET', 'POST'])]
    #[ParamConverter('application', class: Application::class, options: ['mapping' => ['slug' => 'slug']])]
    public function conversation(Application $application, Request $request, MessageRepository $messageRepository): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->getUser());
            $message->setReceiver($application->getCandidat());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
        }

        $messages = $messageRepository->createQueryBuilder('m')
            ->leftJoin('m.application', 'a')
            ->leftJoin('a.job', 'j')
            ->where('a.candidat = :candidat')
            ->andWhere('j.recruter = :recruter')
            ->setParameters([
                'candidat' => $application->getCandidat(),
                'recruter' => $application->getJob()->getRecruter()
            ])
            ->orderBy('createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->renderForm('message/new.html.twig', [
            'messages' => $messages,
            'message' => $message,
            'form' => $form,
        ]);
    }
}
