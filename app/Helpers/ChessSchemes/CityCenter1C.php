<?php

namespace App\Helpers\ChessSchemes;

class CityCenter1C implements ChessSchemeInterface
{
    const NAME = 'CityCenter1C';

    public $offsets = [
        /* 
        * in each pair of params:
        * the first digit is a vertical offset (Y axis)
        * the second digit is a horizontal offset  (X axis)
        */
        'flatNumber' => [0, 0],
        'floor' => [1, 0],
        'rooms' => [0 ,1],
        'area' => [1, 0],
        'price' => [3, 1],
        'isLiving' => [0, 0], // a cell to check if apartment is living or not (0 ,0 - is default value)       
        'flatMatrix' => [3, 7], // [amount of cells, amount of rows]
        'floor_in_flat' => true, // if true - 'floor' is set for each flat, if false - 'floor' is set only fo the 1st flat on floor
        'rooms_in_flat' => true // if true - 'rooms' is set for each flat, if false - 'rooms' is set only fo the 1st flat in column
    ];

    public function filterArea($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $array = explode(' ', $rawValue);
        return floatval(str_replace(',', '.', $array[3]));
    }

    public function filterFloor($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $array = explode(' ', $rawValue);
        return (int)str_replace(',', '.', $array[1]);
    }

    public function filterNumber($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }
    
    public function filterPrice($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return floatval(preg_replace('/\s+/', '', $rawValue));
    }

    public function filterRooms($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $array = explode(' ', $rawValue);
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