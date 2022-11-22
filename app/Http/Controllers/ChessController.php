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
        $spreadsheet = IOFactory::load(storage_path('app/public/example1.xlsx'));
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $sheetData = array();

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
        //$sheetData = $worksheet->toArray(null, true, true, true);
        //echo '<pre>'; var_dump($sheetData); echo '</pre>'; die;

        return Inertia::render('Chess/Add', [
            'chessData' => $sheetData
        ]);
    }

}