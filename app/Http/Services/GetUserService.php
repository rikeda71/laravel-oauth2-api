<?php

namespace App\Http\Services;

use App\Models\User;

class GetUserService
{
    /**
     * @var User
     */
    private $userRepository;

    /**
     * GetUserService constructor.
     * @param User $userRepository
     */
    public function __construct(User $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(int $id): User
    {
        return $this->userRepository->where('id', $id)->first();
    }
}
