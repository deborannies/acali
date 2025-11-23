<?php

namespace Tests\Unit\Core\Database\ActiveRecord;

use Tests\TestCase;
use App\Models\Project;
use App\Models\User;
use Core\Database\ActiveRecord\BelongsToMany;

class BelongsToManyTest extends TestCase
{
    public function test_it_returns_belongs_to_many_instance(): void
    {
        $user = User::findBy(['email' => 'user@teste.com']);
        
        if (!$user) {
            $this->markTestSkipped();
        }

        $project = new Project(['title' => 'Teste Core Instance', 'user_id' => $user->id]);
        $project->save();

        $relation = $project->team();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }
    
    public function test_it_can_attach_and_detach_records(): void
    {
        $user = User::findBy(['email' => 'user@teste.com']);

        if (!$user) {
            $this->markTestSkipped();
        }

        $project = new Project(['title' => 'Teste Framework Logic', 'user_id' => $user->id]);
        $project->save();

        $project->team()->attach($user->id);
        $this->assertEquals(1, $project->team()->count());

        $project->team()->detach($user->id);
        $this->assertEquals(0, $project->team()->count());
    }
}