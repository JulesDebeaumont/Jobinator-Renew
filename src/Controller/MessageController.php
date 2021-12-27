<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Console\Exception\InvalidOptionException;

#[Route('/message')]
class MessageController extends AbstractController
{
    #[Route('/sent', name: 'messages_sent', methods: ['GET'])]
    public function sent(Request $request ,MessageRepository $messageRepository, PaginatorInterface $paginator): Response
    {
        $messagesQuery = $messageRepository->createQueryBuilder('m')
            ->where('m.receiver = :currentUser')
            ->setParameter('currentUser', $this->getUser())
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();

        $messages = $paginator->paginate($messagesQuery, $request->query->getInt('page', 1), 10);

        return $this->render('message/sent.html.twig', [
            'messages' => $messages
        ]);
    }

    #[Route('/received', name: 'message_received', methods: ['GET'])]
    public function received(Request $request ,MessageRepository $messageRepository, PaginatorInterface $paginator): Response
    {
        $messagesQuery = $messageRepository->createQueryBuilder('m')
            ->where('m.sender = :currentUser')
            ->setParameter('currentUser', $this->getUser())
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();

        $messages = $paginator->paginate($messagesQuery, $request->query->getInt('page', 1), 10);

        return $this->render('message/received.html.twig', [
            'messages' => $messages
        ]);
    }

    #[Route('/new', name: 'message_new', methods: ['GET','POST'])]
    public function new(Request $request, User $receiver): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->getUser());
            $message->setReceiver($receiver);

            if ($message->getSender() === $message->getReceiver()) {
                throw new InvalidOptionException("You can't send a message to yourself!");
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }
}
