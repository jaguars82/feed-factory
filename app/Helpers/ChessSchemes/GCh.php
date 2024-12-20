<?php

namespace App\Helpers\ChessSchemes;

class GCh implements ChessSchemeInterface
{
    const NAME = 'GCh';

    public $offsets = [
        /* 
        * in each pair of params:
        * the first digit is a vertical offset (Y axis)
        * the second digit is a horizontal offset  (X axis)
        */
        'flatNumber' => [0, 0],
        'floor' => [0, -1],
        'rooms' => [0, 0], // Chizhov's gallery chess doesn't have amount of rooms
        'area' => [1, 0],
        'price' => [1, 1],
        'isLiving' => [0, 0], // a cell to check if apartment is living or not (0 ,0 - is default value)
        'isEuro' => [0, 0], // optional param for check apartment for 'euro' layout (via isEuro() method) 
        'isStudio' => [0, 0], // optional param for check apartment for 'studio' layout (via isStudio() method) 
        'flatMatrix' => [2, 2], // [amount of cells, amount of rows]
        'floor_in_flat' => false, // if true - 'floor' is set for each flat, if false - 'floor' is set only fo the 1st flat on floor
        'rooms_in_flat' => true // if true - 'rooms' is set for each flat, if false - 'rooms' is set only fo the 1st flat in column
    ];

    public $params = [
        'default_flat_status' => 2, // default status of a flat (in case if the color of the cell is not in the color-scheme of the chess)
        'number_string' => true, // process flat number to form a string with full number (we use it when the number contains not only digits)
        'number_appendix' => true, // process flat number to form a string with number appendix (we use it when the number contains not only digits)
    ];

    public function filterArea($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return floatval(str_replace(',', '.', $rawValue));
    }

    public function filterFloor($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)preg_replace("/[^0-9]/", '', $rawValue);
    }

    public function filterNumber($rawValue)
    {
        if(strpos($rawValue, "/") !== false){
            $parts = explode("/", $rawValue);
        }
        return isset($parts) ? trim($parts[0]) : trim($rawValue);
    }

    /** optional methods for number_string && number_appendix fields (not included in the ChessSchemeInterface) */
    public function filterNumberString($rawValue)
    {
        return strpos($rawValue, "/") !== false ? trim($rawValue) : NULL;
    }

    public function filterNumberAppendix($rawValue)
    {
        if(strpos($rawValue, "/") !== false){
            $parts = explode("/", $rawValue);
        }
        return isset($parts) ? '/'.trim($parts[1]) : NULL;
    }

    public function filterPrice($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return preg_replace("/[^0-9,]/", '', round($rawValue));
    }

    public function filterRooms($rawValue)
    {
        return 0;
    }

    /** Method to check if the apartment is living or not */
    public function isLiving($rawValue) {
        return true;
    }

    public function filterChessFilename($unfilteredValue) {
        return $unfilteredValue;
    }
}