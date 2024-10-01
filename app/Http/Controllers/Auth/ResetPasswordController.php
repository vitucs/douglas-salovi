<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    protected $googleSheetsService;

    public function __construct()
    {
        $this->googleSheetsService = new GoogleSheetsService();
    }

    protected function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    protected function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
        ]);

        // Verifique se o email existe no Google Sheets
        $users = $this->googleSheetsService->getAllRows('usuarios');
        $userIndex = collect($users)->search(function ($user) use ($request) {
            return $user[2] === $request->email; // Supondo que o email esteja na segunda coluna
        });

        if ($userIndex === false) {
            return back()->withErrors(['email' => 'E-mail não encontrado.']);
        }

        // Atualize a senha do usuário
        $users[$userIndex][5] = bcrypt($request->password); // Supondo que a senha esteja na sexta coluna

        // Salve as alterações na planilha
        $this->googleSheetsService->updateRow($userIndex + 1, $users[$userIndex], 'usuarios');

        return redirect()->route('login')->with('status', 'Senha redefinida com sucesso!');
    }
}
