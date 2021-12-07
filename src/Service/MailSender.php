<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailSender
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function registrationMail(string $emailAdress, bool $isCandidat): void
    {
        if ($isCandidat) {
            $email = (new TemplatedEmail())
                ->to($emailAdress)
                ->subject('Thanks for signing up!')
                ->htmlTemplate('emails/welcome.html.twig');
        } else {
            $email = (new TemplatedEmail())
                ->to($emailAdress)
                ->subject('Thanks for signing up!')
                ->htmlTemplate('emails/welcome.html.twig');
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $error) {
            throw new TransportException($error->getMessage());
        }
    }

    
}
