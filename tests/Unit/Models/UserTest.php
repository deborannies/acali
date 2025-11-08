<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function it_is_invalid_without_a_name_or_email()
    {
        $user = new User(['password' => '123']);
        $this->assertFalse($user->isValid());
        $this->assertNotNull($user->errors('name'));
        $this->assertNotNull($user->errors('email'));
    }

    /** @test */
    public function it_is_invalid_without_a_password_when_new()
    {
        $user = new User([
            'name' => 'Test', 
            'email' => 'test@test.com'
        ]);
        
        $this->assertTrue($user->newRecord());
        $this->assertFalse($user->isValid());
        $this->assertNotNull($user->errors('password'));
    }

    /** @test */
    public function password_is_correctly_hashed_on_save()
    {
        $user = new User([
            'name' => 'Hashing Test',
            'email' => 'hash@test.com',
            'password' => 'senha123',
            'role' => 'user'
        ]);

        $user->save();

        $this->assertNull($user->password);
        $this->assertNotEmpty($user->encrypted_password);
        $this->assertNotEquals('senha123', $user->encrypted_password);
        $this->assertTrue($user->authenticate('senha123'));
        $this->assertFalse($user->authenticate('senha_errada'));
    }
}