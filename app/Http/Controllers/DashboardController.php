<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\GoogleSheetsService;
use Google_Service_Sheets_ValueRange;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $sheetsService;

    public function index($range = "A1")
    {
        if (session()->has('logged_in_user')) {
            $loggedInUser = session('logged_in_user');
            $name = $loggedInUser['name'];
            $email = $loggedInUser['email'];
            $company = $loggedInUser['company'];
            $role = $loggedInUser['role'];


            $googleSheetsService = new GoogleSheetsService();
            $userContent = $googleSheetsService->getAllRows('usuarios', $loggedInUser['email']);
            
            session([
                'logged_in_user' => [
                    'name' => $userContent[0][1],
                    'email' => $userContent[0][2],
                    'phone' => $userContent[0][3],
                    'address' => $userContent[0][4],
                    'role' => $userContent[0][7],
                    'company' => $userContent[0][8]
                ]
            ]);

            if (isset($email)) {
                
                $googleSheetsService = new GoogleSheetsService();
                $sheetComprasContent = $googleSheetsService->getAllRows('compras', $email);
                $values = [];
                $equipe = [];

                if ($sheetComprasContent) {
                    $values = $sheetComprasContent;
                }

                $sheetEquipeContent = $googleSheetsService->getAllRows('usuarios', null, $company);
                if ($sheetEquipeContent) {
                    $equipe = $sheetEquipeContent;
                }


                return view('dashboard', compact('values', 'equipe'));
            } else {
                return redirect('/');
            }
        } else {
            return redirect('/');
        }
    }

    public function logout()
    {
        session()->forget('logged_in_user');
        return redirect('/');
    }

    public function order(Request $request)
    {
        $googleSheetsService = new GoogleSheetsService();

        $response = $googleSheetsService->getAllRows('compras');
        $values = $response;

        $lastId = 0;
        if (!empty($values)) {
            $lastId = (int)end($values)[0];
        }

        $newId = $lastId + 1;

        $googleSheetsService->addRow([
            $newId,
            session('logged_in_user')['email'],
            $request->course, 
            $request->value,
            now()->toDateTimeString()
        ], 'compras');
    
        return redirect('/dashboard');
    }

    public function destroyOrder(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $index = $request->input('index');

        $googleSheetsService = new GoogleSheetsService();
        $loggedInUser = session('logged_in_user');

        $response = $googleSheetsService->getAllRows('compras', $loggedInUser['email']);
        $values = $response;

        if (isset($values[$index])) {
            $googleSheetsService->removeRow($index + 1, 'compras');

            return redirect()->back()->with('success', 'Pedido removido com sucesso!');
        }

        return redirect()->back()->with('error', 'Pedido não encontrado!');
    }

    public function destroyEmployer(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $index = $request->input('index');

        $googleSheetsService = new GoogleSheetsService();

        $response = $googleSheetsService->getAllRows('usuarios');
        $values = $response;

        if (isset($values[$index])) {
            $googleSheetsService->removeRow($index + 1, 'usuarios');

            return redirect()->back()->with('success', 'Pedido removido com sucesso!');
        }

        return redirect()->back()->with('error', 'Pedido não encontrado!');
    }

}
