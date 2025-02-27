<?php

namespace App\Helpers\ChessSchemes;

class Accent implements ChessSchemeInterface
{
    const NAME = 'Accent';

    public $offsets = [
        /* 
        * in each pair of params:
        * the first digit is a vertical offset (Y axis)
        * the second digit is a horizontal offset  (X axis)
        */
        'flatNumber' => [0, 0],
        'floor' => [0, -2],
        'rooms' => [1, 0],
        'area' => [2, 0],
        'price' => [6, 0],
        'isLiving' => [0, 0], // a cell to check if apartment is living or not (0 ,0 - is default value)
        'flatMatrix' => [1, 8], // [amount of cells, amount of rows]
        'floor_in_flat' => false, // if true - 'floor' is set for each flat, if false - 'floor' is set only fo the 1st flat on floor
        'rooms_in_flat' => true // if true - 'rooms' is set for each flat, if false - 'rooms' is set only fo the 1st flat in column
    ];
    
    public $params = [
        'default_flat_status' => 2, // default status of a flat (in case if the color of the cell is not in the color-scheme of the chess)
    ];

    public function filterArea($rawValue)
    {
        return floatval(str_replace(',', '.', $rawValue));
    }

    public function filterFloor($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }

    public function filterNumber($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }

    public function filterPrice($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return floatval(str_replace(',', '.', $rawValue));
    }

    public function filterRooms($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $array = str_split($rawValue);
        return (int)$array[0];
    }

    /** Method to check if the apartment is living or not */
    public function isLiving($rawValue)
    {
        return true;
    }

    public function filterChessFilename($unfilteredValue)
    {
        return $unfilteredValue;
    }
}