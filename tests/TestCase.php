<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function signIn($role = 'Administrator', $user = null)
    {
        $user = $user ?: User::factory(['role' => $role])->create();

        $this->actingAs($user);

        return $user;
    }
}
