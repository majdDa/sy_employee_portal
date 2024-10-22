<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateMyPasswordRequest;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $users = $this->userRepository->getAllUsers();
        return Inertia::render('User/UserPage', ['users' => $users]);
    }


    public function toggleActive(Request $request, $id)
    {

        try {
            $user = $this->userRepository->toggleActive($id);

            return Redirect::back()->with('success', 'Category created successfully.');
        } catch (\Exception $e) {

            return Redirect::back()->withErrors([
                'error' => 'Failed to Active User. ' . $e->getMessage(),
            ])->withInput();
        }
    }


    public function changePassword(Request $request, $id)
    {

        $request->validate([
            'newPassword' => 'required|string|min:6',
        ]);


        try {
            $user = $this->userRepository->changePassword($id, $request->newPassword);

            return Redirect::back()->with('success', 'Password changed successfully.');
        } catch (\Exception $e) {

            return Redirect::back()->withErrors([
                'error' => 'Failed to Change Password. ' . $e->getMessage(),
            ])->withInput();
        }
    }


    public function store(StoreUserRequest $request)
    {

        try {
            $user = $this->userRepository->create($request->only('username', 'password', 'role'));
            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function show()
    {

        $user = Auth::user();
        return Inertia::render('Profile/MyProfile', [
            'username' => $user->username,
            'role' => $user->isAdmin ? "Admin" : "User",
        ]);
    }

    public function updateMyPassword(UpdateMyPasswordRequest $request)
    {

        try {
            $user = Auth::user();

            // Update the password
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('profile')->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }
}
