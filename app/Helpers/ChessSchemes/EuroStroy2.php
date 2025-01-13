<?php

namespace App\Helpers\ChessSchemes;

class EuroStroy2 implements ChessSchemeInterface
{
    const NAME = 'EuroStroy2';

    public $offsets = [
        /* 
        * in each pair of params:
        * the first digit is a vertical offset (Y axis)
        * the second digit is a horizontal offset  (X axis)
        */
        'flatNumber' => [0, 0],
        'floor' => [0, -1],
        'rooms' => [1, 0],
        'area' => [2, 0],
        'price' => [4, 0],
        'isLiving' => [0, 0], // a cell to check if apartment is living or not (0 ,0 - is default value)
        'flatMatrix' => [1, 6], // [amount of cells, amount of rows]
        'floor_in_flat' => false, // if true - 'floor' is set for each flat, if false - 'floor' is set only fo the 1st flat on floor
        'rooms_in_flat' => true // if true - 'rooms' is set for each flat, if false - 'rooms' is set only fo the 1st flat in column
    ];

    public $params = [
        'default_flat_status' => 0, // default status of a flat (in case if the color of the cell is not in the color-scheme of the chess)
    ];

    public function filterArea($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $value = trim(str_replace('м²', '', $rawValue));
        return floatval(str_replace(',', '.', $value));
    }

    public function filterFloor($rawValue) {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }

    public function filterNumber($rawValue) {
        if (empty($rawValue)) { return 0; }
        $value = trim(str_replace('№', '', $rawValue));
        return (int)$value;
    }

    public function filterPrice($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $value = trim(str_replace('₽', '', $rawValue));
        return floatval(preg_replace('/\s+/', '', $value));
    }

    public function filterRooms($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)mb_substr($rawValue, 0, 1);
    }

    /** Method to check if the apartment is living or not */
    public function isLiving($rawValue) {
        return true;
    }

    public function filterChessFilename($unfilteredValue) {
        return $unfilteredValue;
    }
}