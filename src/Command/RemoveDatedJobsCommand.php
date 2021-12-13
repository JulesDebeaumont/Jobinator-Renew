<?php

namespace App\Command;

use App\Repository\JobRepository;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'removeDatedJobs',
    description: 'Remove Jobs after a specific period of time',
)]
class RemoveDatedJobsCommand extends Command
{
    private $deadLine;
    private $manager;
    private $jobRepository;

    public function __construct(EntityManagerInterface $manager, JobRepository $jobRepository)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->jobRepository = $jobRepository;
        $this->deadLine = new DateTime("-6 months");
    }

    protected function configure(): void
    {
        /*
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
        */
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $jobs = $this->jobRepository->createQueryBuilder('j')
            ->where('j.updatedAt < :deadLine')
            ->setParameter('deadLine', $this->deadLine)
            ->getQuery()
            ->getResult();

        foreach($jobs as $job) {
            $this->manager->remove($job);
        }
        $this->manager->flush();
        $count = count($jobs);

        $io->success("{$count} jobs removed from the database (deadline : {$this->deadLine->format('Y-m-d')}).");

        return Command::SUCCESS;
    }
}
