<?php

namespace App\Security\Voter;

use App\Entity\Application;
use App\Entity\Candidat;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ApplicationVoter extends Voter
{
    protected const READ = "APPLICATION_READ";

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [
            self::READ
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
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::READ:
                return $this->canRead($subject, $user);
        }

        throw new \LogicException('This code should not be reached! (ApplicationVoter)');
    }


    private function canRead(Application $application, UserInterface $user): bool
    {
        if (!$application->getJob()->getRecruter() === $user) {
            return false;
        }

        return true;
    }
}
