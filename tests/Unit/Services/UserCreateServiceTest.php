<?php

namespace Tests\Unit\Services;

use App\Exceptions\DBExecuteException;
use App\Http\Services\UserCreateService;
use Illuminate\Database\DatabaseManager;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserCreateServiceTest extends TestCase
{
    const Provider = 'test';
    const Name = 'name';
    const Email = 'aaa@test.com';
    const ProviderUserId = 'provider_user_id';

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
        parent::setUp();
        $this->userRepository = Mockery::mock('App\Models\User');
        $this->socialRelationRepository = Mockery::mock('App\Models\SocialRelation');
        $this->dbm = Mockery::mock(DatabaseManager::class);
        $this->target = new UserCreateService($this->userRepository, $this->socialRelationRepository, $this->dbm);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateUserAndSocialRelation(): void
    {
        // given
        $socialRelation = Mockery::mock('App\Models\SocialRelation')
            ->shouldReceive('exists')
            ->withNoArgs()
            ->once()
            ->andReturn(false) // 存在していない
            ->getMock();
        $socialRelations = Mockery::mock('App\Models\SocialRelation')
            ->shouldReceive('where')
            ->withAnyArgs()
            ->once()
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
            ->once()
            ->andReturn($user)
            ->shouldReceive('where->first')
            ->withAnyArgs()
            ->once()
            ->andReturn(null);
        $this->socialRelationRepository
            ->shouldReceive('create')
            ->withAnyArgs()
            ->once()
            ->andReturnNull();
        $this->dbm
            ->shouldReceive('beginTransaction', 'commit')
            ->once();

        // when
        $actual = $this->target->execute(self::Provider, self::Name, self::Email, self::ProviderUserId);

        // then
        self::assertEquals($user, $actual);
    }

    // ユーザが存在しない場合、ソーシャルアカウントも存在しないはずだが一応テスト
    public function testCreateUserAndNoCreateSocialRelation()
    {
        // given
        $socialRelation = Mockery::mock('App\Models\SocialRelation')
            ->shouldReceive('exists')
            ->withNoArgs()
            ->once()
            ->andReturn(true)
            ->getMock();
        $socialRelations = Mockery::mock('App\Models\SocialRelation')
            ->shouldReceive('where')
            ->withAnyArgs()
            ->once()
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
            ->once()
            ->andReturn($user)
            ->shouldReceive('where->first')
            ->withAnyArgs()
            ->once()
            ->andReturn(null);
        $this->dbm
            ->shouldReceive('beginTransaction', 'commit')
            ->once();

        // when
        $actual = $this->target->execute(self::Provider, self::Name, self::Email, self::ProviderUserId);

        // then
        self::assertEquals($actual, $user);
    }

    public function testNoCreateUserAndCreateSocialRelation()
    {
        // given
        $socialRelation = Mockery::mock('App\Models\SocialRelation')
            ->shouldReceive('exists')
            ->withNoArgs()
            ->once()
            ->andReturn(false)
            ->getMock();
        $socialRelations = Mockery::mock('Eloquent')
            ->shouldReceive('where')
            ->withAnyArgs()
            ->once()
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
            ->shouldReceive('where->first')
            ->once()
            ->andReturn($user);
        $this->socialRelationRepository
            ->shouldReceive('create')
            ->withAnyArgs()
            ->once();
        $this->dbm
            ->shouldReceive('beginTransaction', 'commit')
            ->once();

        // when
        $actual = $this->target->execute(self::Provider, self::Name, self::Email, self::ProviderUserId);

        // then
        self::assertEquals($actual, $user);
    }

    public function testThrowExceptionWhenCreateUser()
    {
        // throwable
        $this->expectException(DBExecuteException::class);
        $this->expectExceptionMessage('failed to create application user.');

        // given
        $this->userRepository
            ->shouldReceive('where')
            ->withAnyArgs()
            ->once()
            ->andThrow(new \Exception('dummy'));
        $this->dbm
            ->shouldReceive('beginTransaction', 'rollback')
            ->once();

        // when
        $this->target->execute(self::Provider, self::Name, self::Email, self::ProviderUserId);
    }
}
