<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Core\Database\Database;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Limpa a tabela de usuários depois de cada teste para evitar interferências.
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Database::getDatabaseConn()->exec('TRUNCATE TABLE users;');
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
        $user = new User(
            name: 'Jane Doe',
            email: 'jane.doe@example.com',
            password: 'password123',
            password_confirmation: 'password123'
        );

        $this->assertTrue($user->save());
        $this->assertCount(1, User::all());
        $this->assertEquals('Jane Doe', User::all()[0]->getName());
    }

    /** @test */
    public function it_should_not_create_user_if_password_does_not_match(): void
    {
        $user = new User(
            name: 'Jane Doe',
            email: 'jane.doe@example.com',
            password: 'password123',
            password_confirmation: 'different_password'
        );

        $this->assertFalse($user->save());
        $this->assertCount(0, User::all());
    }

    /** @test */
    public function it_should_find_a_user_by_id(): void
    {
        $user = new User(name: 'Test User', email: 'test@example.com', password: '123', password_confirmation: '123');
        $user->save();

        $foundUser = User::findById($user->getId());

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('Test User', $foundUser->getName());
    }

    /** @test */
    public function it_should_return_null_if_user_not_found_by_id(): void
    {
        $foundUser = User::findById(999);
        $this->assertNull($foundUser);
    }

    /** @test */
    public function it_should_find_a_user_by_email(): void
    {
        $user = new User(name: 'Email Test', email: 'findme@example.com', password: '123', password_confirmation: '123');
        $user->save();

        $foundUser = User::findByEmail('findme@example.com');

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('Email Test', $foundUser->getName());
    }

    /** @test */
    public function it_should_authenticate_user_with_valid_password(): void
    {
        $user = new User(name: 'Auth User', email: 'auth@example.com', password: 'secret_password', password_confirmation: 'secret_password');
        $user->save();

        $foundUser = User::findByEmail('auth@example.com');
        $this->assertTrue($foundUser->authenticate('secret_password'));
    }

    /** @test */
    public function it_should_not_authenticate_user_with_invalid_password(): void
    {
        $user = new User(name: 'Auth User', email: 'auth@example.com', password: 'secret_password', password_confirmation: 'secret_password');
        $user->save();

        $foundUser = User::findByEmail('auth@example.com');
        $this->assertFalse($foundUser->authenticate('wrong_password'));
    }

    /** @test */
    public function it_should_update_an_existing_user(): void
    {
        $user = new User(name: 'Original Name', email: 'original@example.com', password: '123', password_confirmation: '123');
        $user->save();

        $userToUpdate = User::findById($user->getId());
        $userToUpdate->setName('Updated Name');
        $userToUpdate->save();

        $updatedUser = User::findById($user->getId());
        $this->assertEquals('Updated Name', $updatedUser->getName());
        $this->assertCount(1, User::all());
    }

    /** @test */
    public function it_should_delete_a_user(): void
    {
        $user = new User(name: 'To Be Deleted', email: 'delete@example.com', password: '123', password_confirmation: '123');
        $user->save();

        $this->assertCount(1, User::all());

        $user->destroy();

        $this->assertCount(0, User::all());
        $this->assertNull(User::findById($user->getId()));
    }
}
