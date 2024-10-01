<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path(env('GOOGLE_SHEETS_CREDENTIALS_PATH')));
        $this->client->addScope(Sheets::SPREADSHEETS);
        $this->service = new Sheets($this->client);
        $this->spreadsheetId = '1UaAsR5NoFN4kfCZifjFjf46QPpuzXvXrLnRDJ5AAZaQ';
    }

    public function addRow($values, $range = "usuarios!A1")
    {
        $body = new \Google\Service\Sheets\ValueRange([
            'values' => [$values]
        ]);
        $params = ['valueInputOption' => 'RAW'];
        $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);
    }

    public function getAllRows($range = 'usuarios!A1:I', $email = null, $company = null) 
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();
        $matchingRows = [];

        if ($email && $values) {
            foreach ($values as $row) {
                if (isset($row[1]) && $row[1] == $email && $range == 'compras') {
                    $date = new \DateTime($row[4]);
                    $row[4] = $date->format('d/m/Y');

                    $matchingRows[] = $row;
                } else if (isset($row[2]) && $row[2] == $email) {
                    $date = new \DateTime($row[6]);
                    $row[6] = $date->format('d/m/Y');

                    $matchingRows[] = $row;
                }
            }
            $values = $matchingRows;
        }

        if ($company && $values) {
            foreach ($values as $row) {
                if (isset($row[8]) && $row[8] == $company) {
                    $date = new \DateTime($row[6]);
                    $row[6] = $date->format('d/m/Y');

                    $matchingRows[] = $row;
                }
            }
            $values = $matchingRows;
        }

        if(!$company && !$email) {
            foreach ($values as $row) {
                $matchingRows[] = $row;
            }
            $values = $matchingRows;
        }

        return $values;
    }

    public function updateRowInGoogleSheet($request, $rowIndex)
    {
        $updatedRow = $request;

        $updateRange = "usuarios!A" . ($rowIndex + 1) . ":I" . ($rowIndex + 1);

        $body = new \Google_Service_Sheets_ValueRange([
            'values' => [$updatedRow]
        ]);

        $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            $updateRange,
            $body,
            ['valueInputOption' => 'RAW']
        );

        return response()->json(['message' => 'Updated.']);
    }

    public function removeRow($rowIndex, $range='usuarios')
    {
        $sheetId = false;
        $spreadsheet = $this->service->spreadsheets->get($this->spreadsheetId);
        foreach ($spreadsheet->getSheets() as $sheet) {
            if($range == $sheet->getProperties()->getTitle()) {
                $sheetId = $sheet->getProperties()->getSheetId();
            }
        }
        if ($sheetId!=-1) {
            $request = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [
                    new \Google_Service_Sheets_Request([
                        'deleteDimension' => [
                            'range' => [
                                'sheetId' => $sheetId,
                                'dimension' => 'ROWS',
                                'startIndex' => $rowIndex - 1,
                                'endIndex' => $rowIndex,
                            ],
                        ],
                    ]),
                ],
            ]);
    
            $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $request);
        }
    }

    public function saveResetToken($email, $token)
    {
        $values = $this->getAllRows('usuarios');

        foreach ($values as $index => $row) {
            if (isset($row[1]) && $row[1] === $email) {
                $values[$index][2] = $token; 

                $this->updateRow($index + 1, $values[$index]);
                return true; 
            }
        }

        return false;
    }

    public function updateRow($rowIndex, $values, $range = 'usuarios')
{
    $request = new \Google_Service_Sheets_ValueRange([
        'values' => [$values]
    ]);
    
    $this->service->spreadsheets_values->update(
        $this->spreadsheetId,
        "$range!A$rowIndex",
        $request,
        ['valueInputOption' => 'RAW']
    );
}
}
