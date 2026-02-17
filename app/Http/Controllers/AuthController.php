<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Illuminate\Auth\Events\Registered;
use App\Models\User;

class AuthController extends Controller
{
    // Login method 
    public function login(Request $request)
    {
        // Validate data recieved
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Try to login with recieved data
        if (Auth::attempt($credentials)) {
            // Regenerate session to enhance security
            $request->session()->regenerate();
            $user = Auth::user();

            return response()->json([
                'status' => 'success',
                'user' => [
                    'first_name' => $user->first_name,
                    'role' => $user->role
                ]
            ]);
        }
        // If login failed
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    // Logout method
    public function logout(Request $request)
    {
        Auth::logout();
        // Invalidate actual session
        $request->session()->invalidate();
        //Regenerate CSRF token
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out correctly']);
    }

    // Register method
    public function register(Request $request)
    {
        // Validate recieved data
        $data = $request->validate([
            'dni' => 'required|string|unique:users',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'second_last_name' => 'nullable|string|max:100',
            // Age validation
            'birth_date' => 'required|date|before:-18 years',
            'email' => 'required|email|unique:users',
            // Password validation with confirmed parameter
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        $user = User::create([
            'dni' => $data['dni'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'second_last_name' => $data['second_last_name'],
            'birth_date' => $data['birth_date'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            // Make sure the role is always customer
            'role' => 'customer',
        ]);

        // Send confirmation mail
        event(new Registered($user));

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    // Method to verify user
    public function verify(Request $request) {
        
        $user = User::findOrFail($request->route('id'));

        // Hash security verification
        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link'], 403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return redirect('http://localhost:8000/?verified=1');
    }

    // Method to update user info
    public function updateProfile(Request $request)
    {
        // The user is got by the session
        $user = Auth::user();

        // Validation of recieved data
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string',
            'address'    => 'nullable|string',
        ]);

        $user->update($data);

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    // Method to update user password
    public function updatePassword(Request $request)
    {
        // Validate password data
        $request->validate([
            // Current password is correct?
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        // The user is got by the session
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    // Method to start user password recover
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = PasswordFacade::sendResetLink($request->only('email'));

        if ($status === PasswordFacade::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset link sent to your email.']);
        }

        return response()->json(['message' => 'Unable to send reset link.'], 400);
    }
    
    // Method to reset user password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Reset
        $status = PasswordFacade::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === PasswordFacade::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'])
            : response()->json(['message' => 'Invalid token or email.'], 400);
    }
}
