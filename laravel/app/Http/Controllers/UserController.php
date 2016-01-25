<?php

namespace App\Http\Controllers;

// Laravel
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Event;

// Custom
use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    // Create a new user
    public function create(UserRequest $request)
    {
        // Create user based on post input
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        // Is this the first user?
        if($user->id == 1)
        {
            $user->role = 'admin';
        }
        else
        {
            // Otherwise assign to applicant role by default
            $user->role = 'applicant';
        }

        $user->save();
        $this->auth->loginUsingID($user->id);

        // TODO: Send notification emails
        //Event::fire(new UserRegistered($user));

        $request->session()->flash('success', 'Your account has been registered, you are now logged in.');
        return redirect('/');
    }

    // Handle a user logging in
    public function login(UserRequest $request)
    {
        $credentials = array
        (
            'name' => $request->get('name'),
            'password' => $request->get('password')
        );

        if($this->auth->attempt($credentials))
        {
            $request->session()->flash('success', 'You are now logged in!');
        }

       return redirect('/');
    }

    // Log a user out
    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->flash('success', 'You are now logged out!');
        return redirect('/');
    }
}
