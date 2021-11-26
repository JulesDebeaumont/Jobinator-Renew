<?php

namespace App\Security\Voter;

use App\Entity\Job;
use App\Entity\Recruter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class JobEditionVoter extends Voter
{
    protected const EDIT = 'JOB_EDIT';
    protected const DELETE = 'JOB_DELETE';
    protected const POST = 'JOB_POST';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [
            self::EDIT,
            self::DELETE,
            self::POST
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
                return $this->canPost();
        }

        throw new \LogicException('This code should not be reached! (JobEditionVoter)');
    }

    private function canEdit(Job $job, Recruter $recruter): bool
    {
        if ($job->getRecruter() === $recruter) {
            return true;
        }
    }

    private function canDelete(Job $job, Recruter $recruter): bool
    {
        return $this->canEdit($job, $recruter);
    }

    private function canPost(): bool
    {
        return true;
    }
}
