<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Accent;
use CityCenter1C;
use EuroStroy;
use VDK;
use Vybor;
use Krays;
use Razvitie;
use App\Models\Chess;
use App\Models\Feed;

class UpdateFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feeds:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all the active feeds';

    private $columnsMap = array(
		0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z', 26 => 'AA', 27 => 'AB', 28 => 'AC', 29 => 'AD', 30 => 'AE', 31 => 'AF', 32 => 'AG', 33 => 'AH', 34 => 'AI', 35 => 'AJ', 36 => 'AK', 37 => 'AL', 38 => 'AM', 39 => 'AN', 40 => 'AO', 41 => 'AP', 42 => 'AQ', 43 => 'AR', 44 => 'AS', 45 => 'AT', 46 => 'AU', 47 => 'AV', 48 => 'AW', 49 => 'AX', 50 => 'AY', 51 => 'AZ', 52 => 'BA', 53 => 'BB', 54 => 'BC', 55 => 'BD', 56 => 'BE', 57 => 'BF', 58 => 'BG', 59 => 'BH', 60 => 'BI', 61 => 'BJ', 62 => 'BK', 63 => 'BL', 64 => 'BM', 65 => 'BN', 66 => 'BO', 67 => 'BP', 68 => 'BQ', 69 => 'BR', 70 => 'BS', 71 => 'BT', 72 => 'BU', 73 => 'BV', 74 => 'BW', 75 => 'BX', 76 => 'BY', 77 => 'BZ', 78 => 'CA', 79 => 'CB', 80 => 'CC', 81 => 'CD', 82 => 'CE', 83 => 'CF', 84 => 'CG', 85 => 'CH', 86 => 'CI', 87 => 'CJ', 88 => 'CK', 89 => 'CL', 90 => 'CM', 91 => 'CN', 92 => 'CO', 93 => 'CP', 94 => 'CQ', 95 => 'CR', 96 => 'CS', 97 => 'CS', 98 => 'CT', 99 => 'CU', 100 => 'CV', 101 => 'CW', 102 => 'CX', 103 => 'CY', 104 => 'CZ'
	);

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $activeFeeds = Feed::all()->where('is_active', 1);
         
        foreach ($activeFeeds as $feed) {
            
            $dataForXML = array();

            $activeChesses = $feed->chesses->where('is_active', 1);

            foreach ($activeChesses as $chess) {
                $chessData = $this->processChess($chess->id);

                if (!array_key_exists($chessData['complex']['name'], $dataForXML)) {
                    $dataForXML[$chessData['complex']['name']] = array();
                }

                foreach ($chessData['complex']['buildings'] as $building) {
                    if (!array_key_exists($building['name'], $dataForXML[$chessData['complex']['name']])) {
                        $dataForXML[$chessData['complex']['name']][$building['name']] = $building['flats'];
                    } else {
                        $dataForXML[$chessData['complex']['name']][$building['name']] = array_merge( $dataForXML[$chessData['complex']['name']][$building['name']], $building['flats']);
                    }
                }
            }

            $dom = new \DOMDocument("1.0", "utf-8");
            $root = $dom->createElement("complexes");
            
            foreach ($dataForXML as $complexName => $buildings) {
                
                // Newbuilding Complex
                $complexNode = $dom->createElement("complex");
                $complexName = $dom->createElement("name", $complexName);
                $complexNode->appendChild($complexName);

                // Buildings
                $buildingsNode = $dom->createElement("buildings");
                foreach ($buildings as $buildingName => $flats) {
                    $buildingNode = $dom->createElement("building");
                    $buildingName = $dom->createElement("name", $buildingName);
                    $buildingNode->appendChild($buildingName);

                    // Flats
                    $flatsNode = $dom->createElement("flats");
                    foreach ($flats as $flat) {
                        $flatNode = $dom->createElement("flat");
                        $flatId = $dom->createElement("flat_id", $flat['number']);
                        $flatNumber = $dom->createElement("apartment", $flat['number']);
                        $flatFloor = $dom->createElement("floor", $flat['floor']);
                        $flatRoom = $dom->createElement("room", $flat['rooms']);
                        $flatPrice = $dom->createElement("price", $flat['price_cash']);
                        $flatArea = $dom->createElement("area", $flat['area']);
                        $flatStatus = $dom->createElement("status", $flat['status']);
                        $flatSection = $dom->createElement("section", $flat['section']);
                        $flatNode->appendChild($flatId);
                        $flatNode->appendChild($flatNumber);
                        $flatNode->appendChild($flatFloor);
                        $flatNode->appendChild($flatRoom);
                        $flatNode->appendChild($flatPrice);
                        $flatNode->appendChild($flatArea);
                        $flatNode->appendChild($flatStatus);
                        $flatNode->appendChild($flatSection);
                        $flatsNode->appendChild($flatNode);
                    }
                    $buildingNode->appendChild($flatsNode);
                    $buildingsNode->appendChild($buildingNode);
                }
                $complexNode->appendChild($buildingsNode);
                $root->appendChild($complexNode);
            }

            /*
            // Previous mechanism of feed-generating (without groupping by complex & newbuilding)
            foreach ($activeChesses as $chess) {
                $chessData = $this->processChess($chess->id);

                // Newbuilding Complex
                $complexNode = $dom->createElement("complex");
                $complexName = $dom->createElement("name", $chessData['complex']['name']);
                $complexNode->appendChild($complexName);

                // Buildings
                $buildingsNode = $dom->createElement("buildings");
                foreach ($chessData['complex']['buildings'] as $building) {
                    $buildingNode = $dom->createElement("building");
                    $buildingName = $dom->createElement("name", $building['name']);
                    $buildingNode->appendChild($buildingName);

                    // Flats
                    $flatsNode = $dom->createElement("flats");
                    foreach ($building['flats'] as $flat) {
                        $flatNode = $dom->createElement("flat");
                        $flatId = $dom->createElement("flat_id", $flat['number']);
                        $flatNumber = $dom->createElement("apartment", $flat['number']);
                        $flatFloor = $dom->createElement("floor", $flat['floor']);
                        $flatRoom = $dom->createElement("room", $flat['rooms']);
                        $flatPrice = $dom->createElement("price", $flat['price_cash']);
                        $flatArea = $dom->createElement("area", $flat['area']);
                        $flatStatus = $dom->createElement("status", $flat['status']);
                        $flatSection = $dom->createElement("section", $flat['section']);
                        $flatNode->appendChild($flatId);
                        $flatNode->appendChild($flatNumber);
                        $flatNode->appendChild($flatFloor);
                        $flatNode->appendChild($flatRoom);
                        $flatNode->appendChild($flatPrice);
                        $flatNode->appendChild($flatArea);
                        $flatNode->appendChild($flatStatus);
                        $flatNode->appendChild($flatSection);
                        $flatsNode->appendChild($flatNode);
                    }

                    $buildingNode->appendChild($flatsNode);

                    $buildingsNode->appendChild($buildingNode);
                }

                $complexNode->appendChild($buildingsNode);
                $root->appendChild($complexNode);
            } 
            */

            $dom->appendChild($root);

            $dom->save(storage_path('app/public/feeds/'.$feed->id.'.xml'));

        }
        return Command::SUCCESS;
    }

    /**
     * process a particular chess to form an array
     * with chess data
     */
    private function processChess($chessId)
    {
        $chessData = array();

        $chess = Chess::find($chessId);

        $complex = array();
        $complex['name'] = $chess->complex_feed_name;

        $building = array();
        $building['name'] = $chess->building_feed_name;

        // chess file
        if (!empty($chess->file_chess_path) && Storage::exists($chess->file_chess_path)) {
            $spreadsheet = IOFactory::load(storage_path('app/'.$chess->file_chess_path));
            if ($chess->sheet_index !== NULL) {
                $sheets = $spreadsheet->getAllSheets();
                if ($chess->sheet_name == $sheets[$chess->sheet_index]->getTitle()) {
                    $worksheet = $spreadsheet->getSheet($chess->sheet_index);
                } else {
                    $worksheet = $spreadsheet->getSheetByName($chess->sheet_name);
                }
            } else {
                $worksheet = $spreadsheet->getActiveSheet();
            }
        }

        $scheme = $this->chessScheme($chess->scheme);

        if (!empty($chess->color_legend)) {
            $colorLegend = get_object_vars(json_decode($chess->color_legend));
        }
        $hasColorLegend = isset($colorLegend) && (count($colorLegend) > 0) ? true : false;
        
        $entrancesData = json_decode($chess->entrances_data);

        $flats = array();

        foreach ($entrancesData as $entrance) {

            // array with room amount for each flat on the floor in current entrance according to flat's order (index) on the floor
            $roomsByIndex = array();

            // vertical offset (table rows) for current floor
            $currentFloorOffset = 0;
            // iterating floors of the entrance
            for ($i = 1; $i <= $entrance->totalFloors; $i++) {
                
                // horizontal offset (table columns) for current flat on the floor
                $currentFlatOffset = 0;
                
                // current 'floor' for "not floor in flat" chesses (chesses where floor is set for the whole row, not for every flat)
                $rowFloor = 0;

                // iterating flats on the each floor
                // $j - is an index (order) of a flat on a floor
                for ($j = 1; $j <= $entrance->flatsOnFloor; $j++) {
                    $currentFlatStartColumnLetter = $this->getColumnLetterWithOffset($entrance->startCell->column, $currentFlatOffset);
                    $currentFlatStartRow = (int)$entrance->startCell->row + $currentFloorOffset;
                    $flatItem = $this->processFlat($currentFlatStartColumnLetter, $currentFlatStartRow, $scheme, $worksheet);

                    // fill array of rooms amount while processing top row (floor) of the entrance (for not 'rooms in flat')
                    if ($scheme->offsets['rooms_in_flat'] === false && $i === 1) {
                        $roomsByIndex[$j] = $this->getPureValue($worksheet, $scheme, $currentFlatStartRow, $currentFlatStartColumnLetter, 'filterRooms', 'rooms');
                    }

                    // get floor while processing 1st flat on the floor (for "not floor in flat") chess schemes
                    if ($scheme->offsets['floor_in_flat'] === false && $j === 1) {
                        $rowFloor = $this->getPureValue($worksheet, $scheme, $currentFlatStartRow, $currentFlatStartColumnLetter, 'filterFloor', 'floor');
                    }
                    
                    // set floor for "not floor in flat" chess scheme
                    if ($scheme->offsets['floor_in_flat'] === false) {
                        $flatItem['floor'] = $rowFloor;
                    }

                    // set rooms for "not rooms in flat" chess scheme
                    if ($scheme->offsets['rooms_in_flat'] === false) {
                        $flatItem['rooms'] = $roomsByIndex[$j];
                    }

                    $flatItem['section'] = $entrance->number;
                    
                    // flat status
                    if ($hasColorLegend) {
                        $flatStatus = array_search($flatItem['bgcolor'], $colorLegend);

                        switch ($flatStatus) {
                            case 'sale':
                                $flatItem['status'] = 0;
                                break;
                            case 'reserved':
                                $flatItem['status'] = 1;
                                break;
                            case 'sold':
                                $flatItem['status'] = 2;
                                break;
                            default:
                            $flatItem['status'] = property_exists($scheme, 'params') && array_key_exists('default_flat_status', $scheme->params) ? $scheme->params['default_flat_status'] : 0;
                        }
                    } else {
                        $flatItem['status'] = property_exists($scheme, 'params') && array_key_exists('default_flat_status', $scheme->params) ? $scheme->params['default_flat_status'] : 0;
                    }

                    // add flat only it has number and it has status 'isLiving'
                    if ($flatItem['number'] !== 0 && $flatItem['isLiving'] === true) {
                        array_push($flats, $flatItem);
                    }
                    
                    // calculate horizontal offset for the next flat
                    $currentFlatOffset += $scheme->offsets['flatMatrix'][0];
                }
                // calculate vertical offset for the next floor
                $currentFloorOffset += $scheme->offsets['flatMatrix'][1];
            }
        }

        $building['flats'] = $flats;
        $complex['buildings']['building'] = $building;
        
        $chessData['complex'] = $complex;
        // var_dump($chessData);
        return $chessData;
    }

    /**
     * process a group of cells (representing a flat according scheme)
     * and get params (number, price, area etc.)
     */
    private function processFlat($startColumn, $startRow, $scheme, $worksheet)
    {
        $flat = array();

        if ($scheme->offsets['floor_in_flat'] === true) {
            $flat['floor'] = $this->getPureValue($worksheet, $scheme, $startRow, $startColumn, 'filterFloor', 'floor');
        }
        $flat['number'] = $this->getPureValue($worksheet, $scheme, $startRow, $startColumn, 'filterNumber', 'flatNumber');
        $flat['isLiving'] = $this->getPureValue($worksheet, $scheme, $startRow, $startColumn, 'isLiving', 'isLiving');
        $flat['area'] = $this->getPureValue($worksheet, $scheme, $startRow, $startColumn, 'filterArea', 'area');

        if(property_exists($scheme, 'params') && array_key_exists('calculate_price_via_area', $scheme->params)) {
            $flat['price_cash'] = $this->getPureValue($worksheet, $scheme, $startRow, $startColumn, 'filterPrice', 'price', $flat['area']);
        } else {
           $flat['price_cash'] = $this->getPureValue($worksheet, $scheme, $startRow, $startColumn, 'filterPrice', 'price'); 
        }

        if ($scheme->offsets['rooms_in_flat'] === true) {
            $flat['rooms'] = $this->getPureValue($worksheet, $scheme, $startRow, $startColumn, 'filterRooms', 'rooms');
        }

        // flat cell background color (to use it to set the status)
        $flat['bgcolor'] = $worksheet !== null ? $worksheet->getCell($this->getCellAddressByOffset($startRow, $startColumn, $scheme->offsets['flatNumber']))->getStyle()->getFill()->getStartColor()->getRGB() : '';

        return $flat;
    }

    /**
     * class with chess sheme params (offsets, filters)
     */
    private function chessScheme($scheme)
    {
        $createScheme = new $scheme();
        return $createScheme;
    }

    /**
     * Calculate the adress of a cell by given "start" and offset
     */
    private function getCellAddressByOffset($row, $column, $offset)
    {
        if ($offset[0] !== 0) { $row = $row + $offset[0]; }
        if ($offset[1] !== 0) {
            $column = $this->getColumnLetterWithOffset($column, $offset[1]);
        }
        return $column.$row;
    }

    /**
     * Calculate the column (letter) by given start column and offset
     */
    private function getColumnLetterWithOffset($startColumnLetter, $offset)
    {
        $currentColumnKey = array_search($startColumnLetter, $this->columnsMap);
        $targetColumnKey = $currentColumnKey + $offset;
        return $this->columnsMap[$targetColumnKey];
    }

    /**
     * Gets formatted (filtered) value of a cell
     */
    private function getPureValue($worksheet, $scheme, $startRow, $startColumn, $filterMethodName, $offsetFieldName, $additionalFilteringParam = null)
    {
        $pureValue = false;

        $targetCellAddress = $this->getCellAddressByOffset($startRow, $startColumn, $scheme->offsets[$offsetFieldName]);

        if ($worksheet !== null) {
            $targetCell = $worksheet->getCell($targetCellAddress);

            $getValueMethod = $targetCell->isFormula() ? 'getCalculatedValue' : 'getValue';
        
            if ($additionalFilteringParam !== null) {
                $pureValue = $scheme->$filterMethodName(($targetCell)->$getValueMethod(), $additionalFilteringParam);
            } else {
                $pureValue = $scheme->$filterMethodName(($targetCell)->$getValueMethod());    
            }
            
        }

        return $pureValue;
    }

}
