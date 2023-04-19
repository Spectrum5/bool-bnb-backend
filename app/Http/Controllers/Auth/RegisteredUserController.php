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
            'user_image' => ['nullable', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'date_of_birth' => ['nullable'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'unspecified'])],
            'phone_number' => ['nullable', 'numeric'],
            'address' => ['nullable', 'string'],
        ]);

        // $user = User::create([
        //     'first_name' => $request->first_name,
        //     'last_name' => $request->last_name,
        //     'level' => 'EM',
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);

        // Creazione nuovo User
        $newUser = new User;

        $newUser->first_name = $request->first_name;
        $newUser->last_name = $request->last_name;
        $newUser->level = 'EM';
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);

        $newUser->save();

        // Creazione User Detail
        $newUserDetail = new UserDetail();
        
        $newUserDetail->user_id = $newUser->id;
        // if ($request->date_of_birth && $request->date_of_birth != 'null') $newUserDetail->date_of_birth = $request->date_of_birth;
        if ($request->gender) $newUserDetail->gender = $request->gender;
        if ($request->phone_number) $newUserDetail->phone_number = $request->phone_number;
        if ($request->address) $newUserDetail->address = $request->address;

        if ($request->date_of_birth && $request->date_of_birth <= new DateTime('2004-12-31')) {
            $newUserDetail->date_of_birth = $request->date_of_birth;
        }
        else {
            $newUserDetail->date_of_birth = null;
        }

        // $newUserDetail->gender = $request->gender;
        // $newUserDetail->phone_number = $request->phone_number;
        // $newUserDetail->address = $request->address;

        if ($request->file('user_image')) {
            $image = $request->file('user_image');
            $imageName = time() . '.' . $image->extension();
            $newUserDetail->user_image = $imageName;
            $image->storeAs('public/user_images', $imageName);
        }

        $newUserDetail->save();

        event(new Registered($newUser));

        Auth::login($newUser);

        return response()->noContent();
    }

    // public function messages()
// {
    // return [
    //     'price.required' => 'You must have a price.',
    //     'price.numeric' => 'You have invalid characters in the price field'
    // ];
// }
}
