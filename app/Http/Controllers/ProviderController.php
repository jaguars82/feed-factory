<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\grch\Developer;

class ProviderController extends Controller
{
    /**
     * Display the add new provider form
     *
     * @return \Inertia\Response
     */
    public function add(Request $request)
    {
        $developers = Developer::all();

        return Inertia::render('Provider/Add', [
            'developers' => $developers
        ]);
    }
}
