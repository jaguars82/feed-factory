<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Provider;
use App\Models\grch\Developer;

class ProviderController extends Controller
{
    /**
     * Display list of providers
     *
     * @return \Inertia\Response
     */
    public function index(Request $request) {
        $providers = Provider::all();

        return Inertia::render('Provider/Index', [
            'providers' => $providers,
        ]);
    }


    /**
     * Display the add new provider form
     *
     * @return \Inertia\Response
     */
    public function add(Request $request)
    {
        $developers = Developer::all();

        if ($request->isMethod('post')) {
            
            $provider = Provider::create($request->input());
            
            return Redirect::route('provider.index');
        }

        return Inertia::render('Provider/Add', [
            'developers' => $developers,
        ]);
    }
}
