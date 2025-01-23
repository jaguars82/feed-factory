<?php

namespace App\Helpers\ChessSchemes;

class Razvitie implements ChessSchemeInterface
{
    const NAME = 'Razvitie';

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
        'isLiving' => [6, 1], // a cell to check if apartment is living or not (0 ,0 - is default value)
        'flatMatrix' => [2, 9], // [amount of cells, amount of rows]
        'floor_in_flat' => false, // if true - 'floor' is set for each flat, if false - 'floor' is set only fo the 1st flat on floor
        'rooms_in_flat' => true // if true - 'rooms' is set for each flat, if false - 'rooms' is set only fo the 1st flat in column
    ];

    public $params = [
        'number_string' => true, // process flat number to form a string with full number (we use it when the number contains not only digits)
        'number_appendix' => true, // process flat number to form a string with number appendix (we use it when the number contains not only digits)
    ];

    public function filterArea($rawValue)
    {
        if (empty($rawValue)) { return 0; }
        /* $array = explode('(', $rawValue);
        $rawArea = count($array) > 1 ? explode(' ', $array[1]) : [0];
        return floatval(str_replace(',', '.', $rawArea[0])); */
        $value = trim(str_replace('м²', '', $rawValue));
        return floatval(str_replace(',', '.', $value));
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

    /** optional methods for number_string && number_appendix fields (not included in the ChessSchemeInterface) */
    public function filterNumberString($rawValue)
    {
        $value = str_replace('= ', '', $rawValue);
        $value = str_replace(' =', '', $value);

        return strpos($value, "/") !== false ? trim($value) : NULL;
    }

    public function filterNumberAppendix($rawValue)
    {
        $value = str_replace('= ', '', $rawValue);
        $value = str_replace(' =', '', $value);

        if(strpos($value, "/") !== false){
            $parts = explode("/", $value);
        }
        return isset($parts) ? '/'.trim($parts[1]) : NULL;
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

    /** Method to check if the apartment is living or not */
    public function isLiving($rawValue) {
        if (str_contains('неж', $rawValue)) {
            return false;
        }
        return true;
    }

    public function filterChessFilename($unfilteredValue) {
        $charset = mb_detect_encoding($unfilteredValue);
        $convertedString = iconv($charset, "UTF-8", $unfilteredValue);
        $result = str_replace('_', '"', $convertedString);
        return $result;
    }
}