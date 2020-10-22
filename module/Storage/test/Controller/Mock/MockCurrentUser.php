<?php

namespace StorageTest\Controller\Mock;

use Reference\Entity\User;

trait MockCurrentUser
{
    private User $user;
    private bool $currentUserIsset = false;

    public function setCurrentUser(User $user): void
    {
        $this->user = $user;
        $this->currentUserIsset = true;
    }

    protected function currentUser()
    {
        if (! $this->currentUserIsset) {
            $user = new User();
            $this->user = $user;
        }
        return new class($this->user){
            private User $user;
            public function __construct(User $user)
            {
                $this->user = $user;
            }
            public function getDepartment()
            {
                return $this->user->getDepartment();
            }

        };
    }
}
