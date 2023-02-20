<?php

namespace App\Helpers\ChessSchemes;

class Razvitie
{
    public $offsets = [
        /* 
        * in each pair of params:
        * the first digit is a vertical offset (Y axis)
        * the second digit is a horizontal offset  (X axis)
        */
        'flatNumber' => [1, 1],
        'floor' => [1, -1],
        'rooms' => [6, 1],
        'area' => [7, 1],
        'price' => [4, 1],
        'flatMatrix' => [2, 9], // [amount of cells, amount of rows]
        'floor_in_flat' => false // if true - floor is set for each flat, if false - floor is set only fo the 1st flat on floor
    ];

    public function filterArea($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $array = explode('(', $rawValue);
        $rawArea = explode(' ', $array[1]);
        return floatval(str_replace(',', '.', $rawArea[0]));
    }

    public function filterFloor($rawValue) {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }

    public function filterNumber($rawValue) {
        if (empty($rawValue)) { return 0; }
        $value = str_replace('= ', '', $rawValue);
        $value = str_replace(' =', '', $value);
        return (int)preg_replace('/\s+/', '', $value);
    }

    public function filterPrice($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return floatval(preg_replace('/\s+/', '', $rawValue));
    }

    public function filterRooms($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $array = explode('-', $rawValue);
        return (int)$array[0];
    }
}