<?php

namespace App\Helpers\ChessSchemes;

interface ChessSchemeInterface
{
    /** Methods to filter raw values from spreadsheet's cell */
    public function filterArea($rawValue);
    public function filterFloor($rawValue);
    public function filterNumber($rawValue);
    public function filterPrice($rawValue/*, $areaInSquareMeters = null*/);
    public function filterRooms($rawValue);

    /** Method to check if the apartment is living or not */
    public function isLiving($rawValue);

    /** 
     * Filter filename of a chess (in email attachment)
     * (to remove changing part of the name, e.x. date)
     */
    public function filterChessFilename($unfilteredValue);
}