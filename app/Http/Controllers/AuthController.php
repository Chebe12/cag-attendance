<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AuthController handles user authentication (login/logout)
 *
 * This controller manages:
 * - Display login form
 * - Process login with employee_no and password
 * - Process logout with session cleanup
 */
class AuthController extends Controller
{
    /**
     * Show the login form
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user login
     *
     * Validates login credentials (employee_no or email) and password,
     * logs in the user, and redirects to the appropriate dashboard based on user type
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validate the input fields
        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:6',
        ], [
            'login.required' => 'Employee number or email is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        // Determine if the login is an email or employee number
        $loginField = filter_var($validated['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'employee_no';

        // Attempt to authenticate the user
        if (Auth::attempt([$loginField => $validated['login'], 'password' => $validated['password']])) {
            // Authentication successful
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect to appropriate dashboard based on user type
            if ($user->user_type === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $user->firstname);
            } else {
                return redirect()->route('staff.dashboard')->with('success', 'Welcome back, ' . $user->firstname);
            }
        }

        // Authentication failed - redirect back with error
        return back()
            ->withInput($request->only('login'))
            ->withErrors(['login' => 'Invalid credentials. Please check your employee number/email and password.']);
    }

    /**
     * Handle user logout
     *
     * Clears the session and redirects to login page
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Log out the user
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token to prevent CSRF attacks
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
