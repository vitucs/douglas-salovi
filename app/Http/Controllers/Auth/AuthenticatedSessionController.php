<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\GoogleSheetsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
    {
        if (session()->has('logged_in_user')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Valida os dados de login
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        // Obtém todos os dados da planilha
        $googleSheetsService = new GoogleSheetsService();
        $sheetContent = $googleSheetsService->getAllRows();

        foreach ($sheetContent as $row) {
            if (isset($row[2]) && $row[2] == $request->email) {
                if (Hash::check($request->password, $row[5])) {
                    session([
                        'logged_in_user' => [
                            'name' => $row[1],
                            'email' => $row[2],
                            'phone' => $row[3] ?? '',
                            'address' => $row[4] ?? '',
                            'role' => $row[7] ?? '',
                            'company' => $row[8] ?? ''
                            ]
                    ]);

                    return redirect('/dashboard');
                } else {
                    return back()->withErrors(['password' => 'Incorrect Password.'])->withInput();
                }
            }
        }

        // Se o e-mail não for encontrado
        return back()->withErrors(['email' => 'Email not found.'])->withInput();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        session()->forget('logged_in_user');
        return redirect('/');
    }
}
