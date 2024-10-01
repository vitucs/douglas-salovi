<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetsService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $googleSheetsService = new GoogleSheetsService();

        $users = $googleSheetsService->getAllRows('usuarios', $request->email);
        $userIndex = collect($users)->search(function ($user) use ($request) {
            return $user[2] === $request->email;
        });

        if ($userIndex === false) {
            return back()->withErrors(['email' => 'E-mail não encontrado.']);
        }

        $users[$userIndex][5] = bcrypt($request->password);

        $googleSheetsService->updateRow($userIndex + 1, $users[$userIndex], 'usuarios');

        return redirect()->route('login')->with('status', 'Senha redefinida com sucesso!');
    }
}
