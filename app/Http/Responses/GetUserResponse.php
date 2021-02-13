<?php

namespace App\Http\Responses;

use App\Models\User;

class GetUserResponse
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
