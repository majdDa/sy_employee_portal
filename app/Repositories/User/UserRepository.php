<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers()
    {

        $authUserId = auth()->id();
        return User::where('id', '!=', $authUserId)->get();
    }


    public function create(array $data)
    {
        return $user =  User::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'isAdmin' => $data['role'] == 'Admin' ? 1 : 0,
            'is_active' => true
        ]);
    }

    public function toggleActive($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();

        return $user;
    }

    public function changePassword($userId, $newPassword)
    {
        $user = User::findOrFail($userId);
        $user->password = Hash::make($newPassword);
        $user->save();

        return $user;
    }
}
