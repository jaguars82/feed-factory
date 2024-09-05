<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Artisan;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Provider;
use App\Models\Chess;
use App\Models\Feed;
use App\Models\Transport;
use App\Models\grch\Developer;
use App\Models\grch\Newbuilding;
use App\Models\grch\NewbuildingComplex;
use Inertia\Inertia;

class ChessController extends Controller
{
    private $pathToProcessingChessFile;

    public function index(Request $request)
    {
        $chesses = Chess::all();

        return Inertia::render('Chess/Index', [
            'chesses' => $chesses,
        ]);
    }

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
                        $currentChessId = $request->input('chessId');
                        $chessFile = $request->file('chess');

                        // put new chessfile to storage
                        $this->pathToProcessingChessFile = $chessFile->store('chessexamples'); // as an example file for chess configuration form
                        $pathToWorkChessFile = $chessFile->store('chessfiles'); // as an initial work file
                        
                        // find chess model
                        $chessModel = Chess::find($currentChessId);

                        // remove previous file(s)
                        if (!empty($chessModel->example_chess_path) && Storage::exists($chessModel->example_chess_path)) {
                            Storage::delete($chessModel->example_chess_path);
                        }
                        if (!empty($chessModel->file_chess_path) && Storage::exists($chessModel->file_chess_path)) {
                            Storage::delete($chessModel->file_chess_path);
                        }

                        // updating info in data base
                        $chessModel->attachment_filename = $chessFile->getClientOriginalName();
                        $chessModel->example_chess_path = $this->pathToProcessingChessFile;
                        $chessModel->file_chess_path = $pathToWorkChessFile;
                        $chessModel->save();
                    }
                    break;
                case 'switch_sheet':
                    $currentChessId = $request->input('chessId');
                    $chessModel = Chess::find($currentChessId);
                    $selectedSheetIndex = $request->input('sheetIndex');
                    $selectedSheetTitle = $request->input('sheetTitle');
                    $this->pathToProcessingChessFile = $chessModel->file_chess_path;
                    break;
                case 'save_chess_params':
                    $createChess = Chess::create($request->input());
                    $currentChessId = $createChess->id;
                    break;
                case 'save_entrances_data':
                    $currentChessId = $request->input('chessId');
                    $chessModel = Chess::find($currentChessId);
                    $chessModel->fill($request->input());
                    $chessModel->save();
                    break;
                case 'save_feed_transport':
                    $currentChessId = $request->input('chessId');
                    $chessModel = Chess::find($currentChessId);
                    $chessModel->fill($request->input());
                    $chessModel->save();

                    /** refresh feed to add just created chess to it */
                    // Artisan::call('feeds:update');

                    return Redirect::route('chess.index');
            }
        }

        // TO DO in the line below provide a path to existing chess (when we are editing a chess)
        // if (!isset($this->pathToProcessingChessFile)) {$this->pathToProcessingChessFile = 'public/example1.xlsx';}

        $sheetData = array();
        $aviableColors = array();

        if (!empty($this->pathToProcessingChessFile)) {
            $spreadsheet = IOFactory::load(storage_path('app/'.$this->pathToProcessingChessFile));

            if(isset($selectedSheetIndex)) {
                $worksheet = $spreadsheet->getSheet($selectedSheetIndex);
            } else {
                $worksheet = $spreadsheet->getActiveSheet(); 
            }

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

                    // array with cell color indication
                    $cellBgColor1 = $cell->getStyle()->getFill()->getStartColor()->getRGB();
                    $cellBgColor2 = $cell->getStyle()->getFill()->getEndColor()->getRGB();
                    if (!in_array($cellBgColor1, $aviableColors)) {
                        array_push($aviableColors, $cellBgColor1);
                    }

                    $cellItem = [
                        'address' => $cellColumn.$cellRow,
                        'row' => $cellRow,
                        'column' => $cellColumn,
                        'columnNumber' => ++$currentCellNumber,
                        'rawValue' => $cellRawValue,
                        'bgColor1' => $cellBgColor1,
                        'bgColor2' => $cellBgColor2,
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
        
        /** All the sheet */
        $allSheets = [];
        if(isset($spreadsheet)){
            $sheets = $spreadsheet->getAllSheets();
            foreach($sheets as $index => $sheet) {
                array_push($allSheets, ['index' => $index, 'title' => $sheet->getTitle()]);
            }
        }
        
        return Inertia::render('Chess/Add', [
            'currentChessId' => isset($currentChessId) ? $currentChessId : null,
            'developers' => Developer::all(),
            'transports' => Transport::all(),
            'feeds' => Feed::all(),
            'newbuildingComplexes' => Inertia::lazy(fn () => NewbuildingComplex::where('developer_id', $request->post('developerId'))->where('active', 1)->get()),
            'newbuildings' => Inertia::lazy(fn () => Newbuilding::where('newbuilding_complex_id', $request->post('complexId'))->where('active', 1)->get()),
            'providers' => Provider::all(),
            'allSheets' => $allSheets,
            'chessData' => $sheetData,
            'aviableColors' => $aviableColors
        ]);
    }


    public function delete($id)
    {
        $chess = Chess::find($id);

        if ($chess) {

            // remove chess file(s)
            if (!empty($chess->example_chess_path) && Storage::exists($chess->example_chess_path)) {
                Storage::delete($chess->example_chess_path);
            }
            if (!empty($chess->file_chess_path) && Storage::exists($chess->file_chess_path)) {
                Storage::delete($chess->file_chess_path);
            }

            $chess->delete();
            
            return redirect()->route('chess.index');
        } else {
            return redirect()->route('chess.index')->with('error', 'Что-то пошло не так...');
        }
    }

}