<?php

namespace App\Helpers\ChessSchemes;

class VDK
{
    public $offsets = [
        /* 
        * in each pair of params:
        * the first digit is a vertical offset (Y axis)
        * the second digit is a horizontal offset  (X axis)
        */
        'flatNumber' => [0, 0],
        'floor' => [0, -1],
        'rooms' => [0 ,2],
        'area' => [1, 0],
        'price' => [2, 1],
        'flatMatrix' => [3, 3], // [amount of cells, amount of rows]
        'floor_in_flat' => false // if true - floor is set for each flat, if false - floor is set only fo the 1st flat on floor
    ];

    public function filterArea($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (float)$rawValue;
    }

    public function filterFloor($rawValue) {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }

    public function filterNumber($rawValue) {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }

    public function filterPrice($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (float)$rawValue;
    }

    public function filterRooms($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }
}