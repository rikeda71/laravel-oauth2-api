<?php

namespace App\Http\Services;

use Throwable;

use App\Models\SocialRelation;
use App\Models\User;
use Illuminate\Database\DatabaseManager;

class UserCreateService
{
    /**
     * @var User
     */
    private $userRepository;

    /**
     * @var SocialRelation
     */
    private $socialRelationRepository;

    /**
     * @var DatabaseManager
     */
    private $dbm;

    /**
     * UserCreateService constructor.
     * @param User $userRepository
     * @param SocialRelation $socialRelationRepository
     * @param DatabaseManager $dbm
     */
    public function __construct(User $userRepository, SocialRelation $socialRelationRepository, DatabaseManager $dbm)
    {
        $this->userRepository = $userRepository;
        $this->socialRelationRepository = $socialRelationRepository;
        $this->dbm = $dbm;
    }

    /**
     * @param string $provider
     * @param string $name
     * @param string $email
     * @param string $socialUserId
     * @return User
     * @throws Throwable
     */
    public function execute(string $provider, string $name, string $email, string $socialUserId): User
    {
        $this->dbm->beginTransaction();
        try {
            // https://github.com/HeshamAdel007/MoviesApp/blob/master/back-end/app/Http/Controllers/Api/Auth/SocialiteLoginController.php
            // TODO: パスワードも用意できるようにしてみる
            $appUser = $this->userRepository::create([
                'name' => $name,
                'email' => $email,
            ]);
            // ソーシャルログイン用のテーブルのマイグレーションを作ってみる
            $socialRelation = $appUser->socialLogin()->where('provider', $provider)->first();
            if ($socialRelation != null) {
                $this->socialRelationRepository::create([
                    'provider' => $provider,
                    'user_id' => $appUser->id,
                    'provider_user_id' => $socialUserId,
                ]);
            }
            $this->dbm->commit();
        } catch (\Exception $e) {
            $this->dbm->rollback();
            throw new \RuntimeException('failed to create application user.  cause: ' . $e);
        }
        return $appUser;
    }
}
