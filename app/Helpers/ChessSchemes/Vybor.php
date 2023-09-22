<?php

namespace App\Helpers\ChessSchemes;

class Vybor implements ChessSchemeInterface
{
    public $offsets = [
        /* 
        * in each pair of params:
        * the first digit is a vertical offset (Y axis)
        * the second digit is a horizontal offset  (X axis)
        */
        'flatNumber' => [0, 0],
        'floor' => [0, -1],
        'rooms' => [2, 2],
        'area' => [3, 2],
        'price' => [4, 0],
        'isLiving' => [0, 0], // a cell to check if apartment is living or not (0 ,0 - is default value)
        'flatMatrix' => [3, 6], // [amount of cells, amount of rows]
        'floor_in_flat' => false, // if true - 'floor' is set for each flat, if false - 'floor' is set only fo the 1st flat on floor
        'rooms_in_flat' => true // if true - 'rooms' is set for each flat, if false - 'rooms' is set only fo the 1st flat in column
    ];

    public $params = [
        'default_flat_status' => 2, // default status of a flat (in case if the color of the cell is not in the color-scheme of the chess)
        'calculate_price_via_area' => true // use area to calculate the price of the object (multiplicate area and price per meter)
    ];

    public function filterArea($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return floatval(str_replace(',', '.', $rawValue));
    }

    public function filterFloor($rawValue) {
        if (empty($rawValue)) { return 0; }
        return (int)preg_replace("/[^0-9]/", '', $rawValue);
    }

    public function filterNumber($rawValue) {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }

    public function filterPrice($rawValue, $areaInSquareMeters = 0)
    {
        if (empty($rawValue)) { return 0; }

        $rawPricePerMeter = substr($rawValue, 0, strrpos($rawValue, ':'));
        $filteredPricePerMeter = preg_replace("/[^0-9,]/", '', $rawPricePerMeter);
        $pricePerMeter = str_replace(',', '.', $filteredPricePerMeter);

        $price = $pricePerMeter * $areaInSquareMeters;

        return (int)$price;
    }

    public function filterRooms($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        return (int)$rawValue;
    }

    /** Method to check if the apartment is living or not */
    public function isLiving($rawValue) {
        return true;
    }

    public function filterChessFilename($unfilteredValue) {
        $filteredValue = substr($unfilteredValue, 0, strrpos($unfilteredValue, '_'));
        return $filteredValue;
    }
}