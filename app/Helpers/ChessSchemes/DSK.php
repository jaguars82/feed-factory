<?php

namespace App\Helpers\ChessSchemes;

class DSK implements ChessSchemeInterface
{
    public $offsets = [
        /* 
        * in each pair of params:
        * the first digit is a vertical offset (Y axis)
        * the second digit is a horizontal offset  (X axis)
        */
        'flatNumber' => [1, 1],
        'floor' => [1, -1],
        'rooms' => [2, 1],
        'area' => [2, 1],
        'price' => [6, 1],
        'isLiving' => [0, 0], // a cell to check if apartment is living or not (0 ,0 - is default value)
        'isEuro' => [2, 1], // optional param for check apartment for 'euro' layout (via isEuro() method) 
        'isStudio' => [2, 1], // optional param for check apartment for 'studio' layout (via isStudio() method) 
        'flatMatrix' => [7, 13], // [amount of cells, amount of rows]
        'floor_in_flat' => false, // if true - 'floor' is set for each flat, if false - 'floor' is set only fo the 1st flat on floor
        'rooms_in_flat' => true // if true - 'rooms' is set for each flat, if false - 'rooms' is set only fo the 1st flat in column
    ];

    public $params = [
        'default_flat_status' => 2, // default status of a flat (in case if the color of the cell is not in the color-scheme of the chess)
    ];

    public function filterArea($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $rawArea = substr($rawValue, strpos($rawValue, ',') + 1);
        $area = str_replace(' м2', '', trim($rawArea));
        return floatval(str_replace(',', '.', $area));
    }

    public function filterFloor($rawValue) {
        if (empty($rawValue)) { return 0; }
        return (int)preg_replace("/[^0-9]/", '', $rawValue);
    }

    public function filterNumber($rawValue) {
        if (empty($rawValue)) { return 0; }
        return (int)preg_replace("/[^0-9]/", '', $rawValue);
    }

    public function filterPrice($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)preg_replace("/[^0-9]/", '', $rawValue);
    }

    public function filterRooms($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)substr(trim($rawValue), 0, 1);
    }

    /** Method to check if the apartment is living or not */
    public function isLiving($rawValue) {
        return true;
    }

    public function filterChessFilename($unfilteredValue) {
        $filteredValue = $unfilteredValue;
        return $filteredValue;
    }

    /** Optional method to check if the apartment has a euro layout */
    public function isEuro($rawValue) {
        $rawMarker = substr($rawValue, 0, strpos($rawValue, ','));
        $marker = mb_substr($rawMarker, -1);
        return $marker === 'Е';
    }

    /** Optional method to check if the apartment has a studio layout */
    public function isStudio($rawValue) {
        $rawMarker = substr($rawValue, 0, strpos($rawValue, ','));
        if ($rawMarker === 'кв. своб. планир') return true;
        $marker = mb_substr($rawMarker, -1);
        if ($marker === 'с') return true;
        return false;
    }

}