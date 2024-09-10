<?php

namespace App\Security\Voters;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;
        return match ($attribute) {
            self::VIEW => $this->canView($task, $user),
            self::EDIT => $this->canEdit($task, $user),
            self::DELETE => $this->canDelete($task, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canView(Task $task, User $user): bool
    {
        if ($this->canEdit($task, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(Task $task, User $user): bool
    {
        if ($this->isAdmin($user) && !$task->getAuthor()) {
            return true;
        }

        return $user === $task->getAuthor();
    }

    private function canDelete(Task $task, User $user): bool
    {
        if ($this->isAdmin($user) && !$task->getAuthor()) {
            return true;
        }

        return $user === $task->getAuthor();
    }

    private function isAdmin(User $user): bool
    {
        return in_array(User::ROLE_ADMIN, $user->getRoles());
    }
}