<?php

namespace App\Helpers\ChessSchemes;

class Legos implements ChessSchemeInterface
{
    const NAME = 'Legos';

    public $offsets = [
        /* 
        * in each pair of params:
        * the first digit is a vertical offset (Y axis)
        * the second digit is a horizontal offset  (X axis)
        */
        'flatNumber' => [0, 0],
        'floor' => [0, -1],
        'rooms' => [0, 1],
        'area' => [1, 0],
        'price' => [2, 0],
        'isLiving' => [0, 0], // a cell to check if apartment is living or not (0 ,0 - is default value)
        'isEuro' => [0, 0], // optional param for check apartment for 'euro' layout (via isEuro() method) 
        'isStudio' => [0, 0], // optional param for check apartment for 'studio' layout (via isStudio() method) 
        'flatMatrix' => [3, 7], // [amount of cells, amount of rows]
        'floor_in_flat' => false, // if true - 'floor' is set for each flat, if false - 'floor' is set only fo the 1st flat on floor
        'rooms_in_flat' => true // if true - 'rooms' is set for each flat, if false - 'rooms' is set only fo the 1st flat in column
    ];

    public $params = [
        'default_flat_status' => 2, // default status of a flat (in case if the color of the cell is not in the color-scheme of the chess)
    ];

    public function filterArea($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        $arr = explode(' ', $rawValue);
        return floatval(str_replace(',', '.', $arr[count($arr) - 1]));
    }

    public function filterFloor($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)preg_replace("/[^0-9]/", '', $rawValue);
    }

    public function filterNumber($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }

    public function filterPrice($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return preg_replace("/[^0-9,]/", '', $rawValue);
    }

    public function filterRooms($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)preg_replace("/[^0-9,]/", '', $rawValue);
    }

    /** Method to check if the apartment is living or not */
    public function isLiving($rawValue)
    {
        return true;
    }

    public function filterChessFilename($unfilteredValue)
    {
        // check if the argument is a string && not empty
        if (!is_string($unfilteredValue)) {
            return $unfilteredValue;
        }

        // Search the last '-'
        $lastOccurrencePosition = strrpos($unfilteredValue, '-');

        // if substring exists, cut the string
        if ($lastOccurrencePosition !== false) {
            $filteredValue = substr($unfilteredValue, 0, $lastOccurrencePosition);
        } else {
            // if doesn't exist - return unfiltered value
            $filteredValue = $unfilteredValue;
        }

        return $filteredValue;
    }
}