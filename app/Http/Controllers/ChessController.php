<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ChessController extends Controller
{
    /**
     * Display the add new chess form
     *
     * @return \Inertia\Response
     */
    public function add(Request $request)
    {
        return Inertia::render('Chess/Add');
    }

}