<?php

namespace App\Command;

use App\Repository\JobRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ShowJobCount',
    description: 'Show the total job count in the database',
)]
class ShowJobCountCommand extends Command
{
    private $manager;
    private $jobRepository;

    public function __construct(EntityManagerInterface $manager, JobRepository $jobRepository)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->jobRepository = $jobRepository;
    }

    protected function configure(): void
    {
        $this
            ->addOption('last-month', null, InputOption::VALUE_NONE, 'Show the last month created job');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('last-month')) {
            $jobs = $this->jobRepository->createQueryBuilder('j')
                ->where('j.updatedAt > :lastMonth')
                ->setParameter('lastMonth', new DateTime("-1 months"))
                ->getQuery()
                ->getResult();
            $count = count($jobs);
            $io->success("{$count} job(s) hase been created since last month.");
        } else {
            $jobs = $this->jobRepository->findAll();
            $count = count($jobs);
            $io->success("There is {$count} job(s) registered in the database.");
        }

        return Command::SUCCESS;
    }
}
