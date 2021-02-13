<?php

namespace Tests\Unit\Services;

use App\Exceptions\UserCreateException;
use App\Http\Services\UserCreateService;
use App\Models\SocialRelation;
use App\Models\User;
use Illuminate\Database\DatabaseManager;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserCreateServiceTest extends TestCase
{
    const Provider = 'test';
    const Name = 'name';
    const Email = 'aaa@test.com';
    const UserId = 'user_id';
    const providerUserId = 'provider_user_id';

    /**
     * @var Mockery\MockInterface
     */
    private $userRepository;

    /**
     * @var Mockery\MockInterface
     */
    private $socialRelationRepository;

    /**
     * @var Mockery\MockInterface
     */
    private $dbm;

    /**
     * @var UserCreateService
     */
    private $target;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->userRepository = Mockery::mock(User::class);
        $this->socialRelationRepository = Mockery::mock(SocialRelation::class);
        $this->dbm = Mockery::mock(DatabaseManager::class);
        $this->target = new UserCreateService($this->userRepository, $this->socialRelationRepository, $this->dbm);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function testCreateUserAndSocialRelation(): void
    {
        // given
        $obj = new SocialRelation();
        $obj->fill(['provider' => self::Provider]);
        $socialRelation = Mockery::mock(SocialRelation::class)
            ->shouldReceive('first')
            ->withNoArgs()
            ->times(1)
            ->andReturn($obj)
            ->getMock();
        $socialRelations = Mockery::mock(SocialRelation::class)
            ->shouldReceive('where')
            ->withArgs(['provider', self::Provider])
            ->times(1)
            ->andReturn($socialRelation)
            ->getMock();
        $user = Mockery::mock(User::class)
            ->shouldReceive('socialLogin')
            ->andReturn($socialRelations)
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(0)
            ->getMock();
        $this->userRepository
            ->shouldReceive('create')
            ->times(1)
            ->andReturn($user);
        $this->socialRelationRepository
            ->shouldReceive('create')
            ->withAnyArgs()
            ->times(1);
        $this->dbm
            ->shouldReceive('beginTransaction', 'commit')
            ->times(1);

        // when
        $actual = $this->target->execute(self::Provider, self::Name, self::Email, self::UserId);

        // then
        self::assertEquals($user, $actual);
    }

    public function testCreateUserAndNoCreateSocialRelation()
    {
        // given
        $socialRelation = Mockery::mock('App\Models\SocialRelation')
            ->shouldReceive('first')
            ->withNoArgs()
            ->times(1)
            ->andReturn(null)
            ->getMock();
        $socialRelations = Mockery::mock('App\Models\SocialRelation')
            ->shouldReceive('where')
            ->withAnyArgs()
            ->times(1)
            ->andReturn($socialRelation)
            ->getMock();
        $user = Mockery::mock('App\Models\User')
            ->shouldReceive('socialLogin')
            ->andReturn($socialRelations)
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(0)
            ->getMock();
        $this->userRepository
            ->shouldReceive('create')
            ->withAnyArgs()
            ->times(1)
            ->andReturn($user);
        $this->dbm
            ->shouldReceive('beginTransaction', 'commit')
            ->times(1);

        // when
        $this->target->execute(self::Provider, self::Name, self::Email, self::UserId);

        // then
        $this->assertTrue(true);
    }

    public function testThrowExceptionWhenCreateUser()
    {
        // throwable
        $this->expectException(UserCreateException::class);
        $this->expectExceptionMessage('failed to create application user.');

        // given
        $this->userRepository
            ->shouldReceive('create')
            ->withAnyArgs()
            ->times(1)
            ->andThrow(new \Exception('dummy'));
        $this->dbm
            ->shouldReceive('beginTransaction', 'rollback')
            ->times(1);

        // when
        $this->target->execute(self::Provider, self::Name, self::Email, self::UserId);
    }
}
