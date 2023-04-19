<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use DateTime;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

// Helpers
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:64'],
            'last_name' => ['required', 'string', 'max:64'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'date_of_birth' => ['nullable'],
        ]);

        $newUser = new User;

        $newUser->first_name = Str::lower($request->first_name);
        $newUser->last_name = Str::lower($request->last_name);
        $newUser->email = Str::lower($request->email);
        $newUser->date_of_birth = $request->date_of_birth;
        $newUser->password = Hash::make($request->password);

        $newUser->save();

        event(new Registered($newUser));

        Auth::login($newUser);

        return response()->noContent();
    }
}