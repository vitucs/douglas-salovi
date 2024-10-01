<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function reset(Request $request)
    {
        $request->session()->forget('logged_in_user');

        return redirect()->route('login');
    }

    public function update(Request $request)
    {        
        $googleSheetsService = new GoogleSheetsService();
        $sheetContent = $googleSheetsService->getAllRows();
        $updatedIndex = 0;
        if ($sheetContent != null) {
            foreach ($sheetContent as $index => $row) {
                if (isset($row[2]) && $row[2] == $request->email) {
                    $updatedIndex = $index;
                }
            }
        }

        $googleSheetsService->updateRowInGoogleSheet([
            $sheetContent[$updatedIndex][0],
            $request->name ?? '',
            $sheetContent[$updatedIndex][2],
            $request->phone ?? '',
            $request->address ?? '',
            $sheetContent[$updatedIndex][5],
            now()->toDateTimeString(),
            $request->role ?? '',
            $request->company ?? ''
        ], $updatedIndex);

        session([
            'logged_in_user' => [
                'name' => $request->name,
                'email' => $sheetContent[$updatedIndex][2],
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => $sheetContent[$updatedIndex][5],
                'role' => $request->role,
                'company' => $request->company
            ]
        ]);
    
        return redirect()->back()->with('success', 'Updated!');
    }
}
