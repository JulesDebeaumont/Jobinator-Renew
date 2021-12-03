<?php

namespace App\Security\Voter;

use App\Entity\Candidat;
use App\Entity\Job;
use App\Entity\Recruter;
use App\Entity\User;
use App\Repository\CandidatRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class JobEditionVoter extends Voter
{
    protected const EDIT = 'JOB_EDIT';
    protected const DELETE = 'JOB_DELETE';
    protected const POST = 'JOB_POST';
    protected const APPLY = 'JOB_APPLY';
    // private $candidatRepository;

    /*
    public function __construct(CandidatRepository $candidatRepository)
    {
        $this->candidatRepository = $candidatRepository;
    }
    */

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [
            self::EDIT,
            self::DELETE,
            self::POST,
            self::APPLY
        ])) {
            return false;
        }

        if (!$subject instanceof Job) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // Ne pas oublier de mettre les attributs de User en protected avec l'héritage
        // Autraument le $token->getUser() va pas pouvoir check correctement et instant logout
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
            case self::POST:
                return $this->canPost($user);
            case self::APPLY:
                return $this->canApply($subject, $user);
        }

        throw new \LogicException('This code should not be reached! (JobEditionVoter)');
    }


    private function canEdit(Job $job, User $user): bool
    {
        if (!$this->isRecruter($user)) {
            return false;
        }

        if ($job->getRecruter() !== $user) {
            return false;
        }

        return true;
    }


    private function canDelete(Job $job, User $user): bool
    {
        return $this->canEdit($job, $user);
    }


    private function canPost(User $user): bool
    {
        if (!$this->isRecruter($user)) {
            return false;
        }

        return true;
    }


    private function canApply(Job $job, User $user): bool
    {
        if (!$this->isCandidat($user)) {
            return false;
        }

        /*
        $results = $this->candidatRepository->createQueryBuilder('c')
            ->leftJoin('c.applications', 'a')
            ->where('c = :currentUser', 'a.job = :currentJob')
            ->setParameters([
                'currentUser' => $user,
                'currentJob' => $job
            ])
            ->getQuery()
            ->getResult();
        
        dump($results);
        */

        // Ici on est sûr que le $user est une instance de Candidat
        foreach ($user->getApplications() as $application) {
            if ($application->getJob() === $job) {
                return false;
            }
        }

        return true;
    }


    /**
     * Tell if user is recruter or not
     */
    private function isRecruter(User $user): bool
    {
        return $user instanceof Recruter;
    }


    /**
     * Tell if user is candidat or not
     */
    private function isCandidat(User $user): bool
    {
        return $user instanceof Candidat;
    }
}
