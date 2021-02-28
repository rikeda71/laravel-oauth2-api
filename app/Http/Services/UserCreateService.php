<?php

namespace App\Http\Services;

use App\Exceptions\DBExecuteException;
use App\Models\SocialRelation;
use App\Models\User;
use Illuminate\Database\DatabaseManager;
use Throwable;

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
            // ユーザが存在していない場合は作成
            // TODO: パスワードも用意できるようにしてみる
            $appUser = $this->userRepository->where('email', $email)->first();
            if (!$appUser) {
                $appUser = $this->userRepository->create([
                    'name' => $name,
                    'email' => $email,
                ]);
            }
            // ユーザが存在していてもこのsocialアカウントでのログインが初めての時はソーシャルアカウントの情報を保存
            if (!$appUser->socialLogin()->where('provider_user_id', $socialUserId)->exists()) {
                $this->socialRelationRepository->create([
                    'provider' => $provider,
                    'user_id' => $appUser->id,
                    'provider_user_id' => $socialUserId,
                ]);
            }
            $this->dbm->commit();
        } catch (\Exception $e) {
            $this->dbm->rollback();
            throw new DBExecuteException('failed to create application user.  cause: ' . $e);
        }
        return $appUser;
    }
}
