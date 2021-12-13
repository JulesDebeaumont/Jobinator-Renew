<?php

namespace App\Service;

use App\Entity\Application;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
            echo ($error->getMessage());
        }
    }


    public function applicationMail(Application $application): void
    {
        $recruterAdress = $application->getJob()->getRecruter()->getEmail();
        $recruterEmail = (new TemplatedEmail())
            ->to($recruterAdress)
            ->subject('Someone applied to your job!')
            ->htmlTemplate('emails/welcome.html.twig');

        $candidatAdress = $application->getCandidat()->getEmail();
        $candidatEmail = (new TemplatedEmail())
            ->to($candidatAdress)
            ->subject('You applied to a job!')
            ->htmlTemplate('emails/welcome.html.twig');

        try {
            $this->mailer->send($recruterEmail);
            $this->mailer->send($candidatEmail);
        } catch (TransportExceptionInterface $error) {
            echo ($error->getMessage());
        }
    }


    public function deleteAccountMail(string $email): void
    {
        $email = (new TemplatedEmail())
            ->to($email)
            ->subject('Your leaving us, too bad !')
            ->htmlTemplate('emails/welcome.html.twig');

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $error) {
            echo ($error->getMessage());
        }
    }
}
