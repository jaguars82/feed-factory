<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Transport;

class TransportController extends Controller
{
    /**
     * Display list of transports
     *
     * @return \Inertia\Response
     */
    public function index(Request $request) {
        $transports = Transport::all();

        return Inertia::render('Transport/Index', [
            'transports' => $transports,
        ]);
    }


    /**
     * Display add new transport form
     *
     * @return \Inertia\Response
     */
    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            
            $transport = Transport::create($request->input());
            
            return Redirect::route('transport.index');
        }

        return Inertia::render('Transport/Add');
    }
}
