<?php

namespace App\Security\Voter;

use App\Entity\Job;
use App\Entity\Recruter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class JobEditionVoter extends Voter
{
    protected const EDIT = 'JOB_EDIT';
    protected const DELETE = 'JOB_DELETE';
    protected const POST = 'JOB_POST';

    protected function supports(string $attribute, $subject): bool
    {
        if (in_array($attribute, [
            self::EDIT,
            self::DELETE,
            self::POST
        ])) {
            return false;
        }
        if ($subject instanceof Job) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Recruter) {
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
        throw new \LogicException('This code should not be reached!');
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
