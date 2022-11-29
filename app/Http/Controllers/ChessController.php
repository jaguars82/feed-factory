<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Inertia\Inertia;

class ChessController extends Controller
{
    /**
     * Display the add new chess form
     *
     * @return \Inertia\Response
     */
    public function add(Request $request)
    {
        /* handle ajax post-requests from form */
        if ($request->isMethod('post')) {
            if ($request->hasFile('chess')) {
               //echo '<pre>'; var_dump($request->file('chess')); echo '</pre>'; die; 
               $chessFile = $request->file('chess');
               $pathToChessFile = $chessFile->store('chessexamples');
               //echo '<pre>'; var_dump($pathToChessFile); echo '</pre>'; die;
            }
        }

        // TO DO in the line below provide a path to existing chess (when we are aditing a chess)
        // if (!isset($pathToChessFile)) {$pathToChessFile = 'public/example1.xlsx';}

        $sheetData = array();

        if (isset($pathToChessFile) && !empty($pathToChessFile)) {
            //$spreadsheet = IOFactory::load(storage_path('app/public/example1.xlsx'));
            $spreadsheet = IOFactory::load(storage_path('app/'.$pathToChessFile));
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $currentCellNumber = 0;
                foreach ($cellIterator as $cell) {
                    $cellColumn = $cell->getColumn();
                    $cellRow = $cell->getRow();
                    $cellRawValue = $cell->getValue();
                    if ($cellRawValue === '#NULL!') {
                        $cellRawValue = null;
                    }
                    $cellItem = [
                        'address' => $cellColumn.$cellRow,
                        'row' => $cellRow,
                        'column' => $cellColumn,
                        'columnNumber' => ++$currentCellNumber,
                        'rawValue' => $cellRawValue,
                        'bgColor1' => $cell->getStyle()->getFill()->getStartColor()->getRGB(),
                        'bgColor2' => $cell->getStyle()->getFill()->getEndColor()->getRGB(),
                        'borders' => [
                            'top' => $cell->getStyle()->getBorders()->getTop()->getBorderStyle(),
                            'right' => $cell->getStyle()->getBorders()->getRight()->getBorderStyle(),
                            'bottom' => $cell->getStyle()->getBorders()->getBottom()->getBorderStyle(),
                            'left' => $cell->getStyle()->getBorders()->getleft()->getBorderStyle()
                        ]
                    ];
                    $sheetData[$cellRow][$cellColumn] = $cellItem;
                    //echo '<pre>'; var_dump($cellItem); echo '</pre>'; die;
                }
            }
        }

        //echo '<pre>'; var_dump($sheetData); echo '</pre>'; die;

        return Inertia::render('Chess/Add', [
            'chessData' => $sheetData
        ]);
    }

}