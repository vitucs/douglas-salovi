<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleSheetsService;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View|RedirectResponse
    {
        if (session()->has('logged_in_user')) {
            return redirect()->route('dashboard');
        }

        // Retorna a view de login caso não haja um usuário logado
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/'
            ],
        ]);

        $googleSheetsService = new GoogleSheetsService();
        $sheetContent = $googleSheetsService->getAllRows();

        if ($sheetContent != null) {
            foreach ($sheetContent as $row) {
                if (isset($row[2]) && $row[2] == $request->email) {
                    return back()->withErrors(['email' => 'Email already exists.'])->withInput();
                }
            }
        }

        $googleSheetsService->addRow([
            'user',
            $request->name,
            $request->email,
            $request->phone,
            $request->address,
            bcrypt($request->password),
            now()->toDateTimeString(),
            $request->role,
            $request->company
        ]);

        session([
            'logged_in_user' => [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'role' => $request->role,
                'company' => $request->company,
                'password' => bcrypt($request->password)
            ]
        ]);
    
        return redirect('/dashboard');
    }
}
