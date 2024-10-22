<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function getAllUsers();
    public function toggleActive($userId);
    public function changePassword($userId, $newPassword);
    public function create(array $data);
}
