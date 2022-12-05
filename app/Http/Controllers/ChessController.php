<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Provider;
use App\Models\grch\Developer;
use App\Models\grch\Newbuilding;
use App\Models\grch\NewbuildingComplex;
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
            switch ($request->input('operation')) {
                case 'load_chess_example':
                    if ($request->hasFile('chess')) {
                        $chessFile = $request->file('chess');
                        $pathToChessFile = $chessFile->store('chessexamples');
                    }
                    break;
            }
        }

        // TO DO in the line below provide a path to existing chess (when we are editing a chess)
        // if (!isset($pathToChessFile)) {$pathToChessFile = 'public/example1.xlsx';}

        $sheetData = array();

        if (isset($pathToChessFile) && !empty($pathToChessFile)) {
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
                }
            }
        }
        //echo '<pre>'; var_dump($request->post('developerId')); echo '</pre>'; die;
        return Inertia::render('Chess/Add', [
            'developers' => Developer::all(),
            'newbuildingComplexes' => Inertia::lazy(fn () => NewbuildingComplex::where('developer_id', $request->post('developerId'))->where('active', 1)->get()),
            'newbuildings' => Inertia::lazy(fn () => Newbuilding::where('newbuilding_complex_id', $request->post('complexId'))->where('active', 1)->get()),
            'providers' => Provider::all(),
            'chessData' => $sheetData
        ]);
    }

}