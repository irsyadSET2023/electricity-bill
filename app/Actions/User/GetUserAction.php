<?php

namespace App\Actions\User;

use App\Models\User;

final class CreateUserAction
{
    public function handle(): User
    {

        return User::first();
    }
}
