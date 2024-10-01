<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Services\GoogleSheetsService;
use App\Mail\PasswordResetMail; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $googleSheetsService = new GoogleSheetsService();
        
        $users = $googleSheetsService->getAllRows('usuarios');

        $user = null;
        foreach ($users as $row) {
            if (isset($row[2]) && $row[2] === $request->email) { 
                $user = $row;
                break;
            }
        }

        if (!$user) {
            return back()->withErrors(['email' => __('Email não encontrado.')]);
        }

        session([
            'email_to_reset_password' => [
                'email' => $request->email,
            ]
        ]);
        
        $token = Str::random(60);

        $googleSheetsService->saveResetToken($user[0], $token);

        Mail::to($request->email)->send(new PasswordResetMail($token));

        return back()->with('status', __('Link de redefinição de senha enviado.'));
    }

}
