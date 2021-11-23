<?php

namespace App\Security\Voter;

use App\Entity\Candidat;
use App\Entity\Recruter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserEditionVoter extends Voter
{
    protected const EDIT = 'USER_EDIT';
    protected const DELETE = 'USER_DELETE';
    protected const SHOW = 'USER_SHOW';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [
            self::EDIT,
            self::DELETE,
            self::SHOW
        ])) {
            return false;
        }

        if ($subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Candidat || !$user instanceof Recruter) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
            case self::SHOW:
                return $this->canShow($subject, $user);
        }

        throw new \LogicException('This code should not be reached! (UserEditionVoter)');
    }

    private function canEdit(User $user, User $currentUser)
    {
        return $user->getId() === $currentUser->getId();
    }

    private function canDelete(User $user, User $currentUser)
    {
        return $this->canEdit($user, $currentUser);
    }

    private function canShow(User $user, User $currentUser)
    {
        return $this->canEdit($user, $currentUser);
    }
}
