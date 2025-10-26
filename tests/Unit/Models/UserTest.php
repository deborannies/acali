<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Core\Database\Database;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function tearDown(): void
    {
        Database::getDatabaseConn()->exec('TRUNCATE TABLE users;');
        parent::tearDown();
    }

    /** @test */
    public function it_can_set_and_get_user_properties(): void
    {
        $user = new User();
        $user->setName('John Doe');
        $user->setEmail('john.doe@example.com');

        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john.doe@example.com', $user->getEmail());
    }

    /** @test */
    public function it_should_create_a_new_user(): void
    {
        $user = new User([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'password123',
        ]);

        $this->assertTrue($user->save());
        $allUsers = User::all(10, 0);
        $this->assertCount(1, $allUsers);
        $this->assertEquals('Jane Doe', $allUsers[0]->getName());
    }

    /** @test */
    public function it_should_not_create_user_if_password_missing(): void
    {
        $user = new User(['name' => 'Jane Doe', 'email' => 'jane.doe@example.com']);
        $this->assertFalse($user->save());
        $this->assertTrue($user->hasErrors());
        $this->assertEquals('A senha é obrigatória.', $user->errors('password'));
        $this->assertCount(0, User::all(10, 0));
    }

    /** @test */
    public function it_should_not_create_user_if_name_missing(): void
    {
        $user = new User(['email' => 'jane.doe@example.com', 'password' => '123']);
        $this->assertFalse($user->save());
        $this->assertTrue($user->hasErrors());
        $this->assertEquals('O nome é obrigatório.', $user->errors('name'));
    }

    /** @test */
    public function it_should_find_a_user_by_id(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
        ]);
        $user->save();

        $foundUser = User::findById($user->getId());
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('Test User', $foundUser->getName());
    }

    /** @test */
    public function it_should_return_null_if_user_not_found_by_id(): void
    {
        $this->assertNull(User::findById(999));
    }

    /** @test */
    public function it_should_find_a_user_by_email(): void
    {
        $user = new User([
            'name' => 'Email Test',
            'email' => 'findme@example.com',
            'password' => '123',
        ]);
        $user->save();

        $foundUser = User::findByEmail('findme@example.com');
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('Email Test', $foundUser->getName());
    }

    /** @test */
    public function it_should_authenticate_user_with_valid_password(): void
    {
        $user = new User([
            'name' => 'Auth User',
            'email' => 'auth@example.com',
            'password' => 'secret_password',
        ]);
        $user->save();

        $foundUser = User::findByEmail('auth@example.com');
        $this->assertTrue($foundUser->authenticate('secret_password'));
    }

    /** @test */
    public function it_should_not_authenticate_user_with_invalid_password(): void
    {
        $user = new User([
            'name' => 'Auth User',
            'email' => 'auth@example.com',
            'password' => 'secret_password',
        ]);
        $user->save();

        $foundUser = User::findByEmail('auth@example.com');
        $this->assertFalse($foundUser->authenticate('wrong_password'));
    }

    /** @test */
    public function it_should_update_an_existing_user(): void
    {
        $user = new User([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'password' => '123',
        ]);
        $user->save();

        $userToUpdate = User::findById($user->getId());
        $userToUpdate->setName('Updated Name');
        $userToUpdate->save();

        $updatedUser = User::findById($user->getId());
        $this->assertEquals('Updated Name', $updatedUser->getName());
        $this->assertCount(1, User::all(10, 0));
    }

    /** @test */
    public function it_should_delete_a_user(): void
    {
        $user = new User([
            'name' => 'To Be Deleted',
            'email' => 'delete@example.com',
            'password' => '123',
        ]);
        $user->save();
        $userId = $user->getId();

        $this->assertCount(1, User::all(10, 0));

        $user->destroy();

        $this->assertCount(0, User::all(10, 0));
        $this->assertNull(User::findById($userId));
    }
}