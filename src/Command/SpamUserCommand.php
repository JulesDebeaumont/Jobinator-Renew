<?php

namespace App\Command;

use App\Repository\CandidatRepository;
use App\Repository\RecruterRepository;
use App\Repository\UserRepository;
use App\Service\MailSender;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'SpamUser',
    description: 'Send useless mail to users',
)]
class SpamUserCommand extends Command
{
    private $mailSender;
    private $candidatRepository;
    private $recruterRepository;
    private $userRepository;

    public function __construct(
        MailSender $mailSender, 
        CandidatRepository $candidatRepository, 
        RecruterRepository $recruterRepository,
        UserRepository $userRepository
        )
    {
        parent::__construct();
        $this->mailSender = $mailSender;
        $this->candidatRepository = $candidatRepository;
        $this->recruterRepository = $recruterRepository;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('userType', InputArgument::OPTIONAL, 'Type of the user: "Candidat" or "Recruter"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('userType');

        if ($arg1) {
            switch ($arg1) {
                case "Candidat":
                    $candidats = $this->candidatRepository->findAll();
                    foreach ($candidats as $candidat) {
                        $this->mailSender->uselessMail($candidat->getEmail());
                    }
                    $io->success('A useless email has been sent to all candidats. Well done!');
                    break;

                case "Recruter":
                    $recruters = $this->recruterRepository->findAll();
                    foreach ($recruters as $recruter) {
                        $this->mailSender->uselessMail($recruter->getEmail());
                    }
                    $io->success('A useless email has been sent to all recruters. Well done!');
                    break;
                    
                default:
                    $io->error('Wrong value for argument userType');
                    break;
            }
        } else {
            $users = $this->userRepository->findAll();
            foreach ($users as $user) {
                $this->mailSender->uselessMail($user->getEmail());
            }
            $io->success('A useless email has been sent to all users. Well done!');
        }

        return Command::SUCCESS;
    }
}
