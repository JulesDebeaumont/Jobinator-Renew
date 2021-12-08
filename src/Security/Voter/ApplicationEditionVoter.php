<?php

namespace App\Security\Voter;

use App\Entity\Application;
use App\Entity\Recruter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ApplicationEditionVoter extends Voter
{
    protected const SHOW = 'APPLICATION_SHOW';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [
            self::SHOW
        ])) {
            return false;
        }

        if (!$subject instanceof Application) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::SHOW:
                return $this->canShow($subject, $user);
        }

        throw new \LogicException('This code should not be reached! (ApplicationEditionVoter)');
    }

    private function canShow(Application $application, User $user): bool
    {
        if (!$this->isRecruter($user)) {
            return false;
        }

        // getValues() pour mettre sous forme de tableau
        if (!in_array($application->getJob(), $user->getJobs()->getValues())) {
            return false;
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
}
