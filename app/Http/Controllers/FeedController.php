<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feed;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Provider;

class FeedController extends Controller
{
    /**
     * Display list of feeds
     *
     * @return \Inertia\Response
     */
    public function index(Request $request) {
        $feeds = Feed::all();

        foreach ($feeds as $feed) {
           $feed['provider'];
        }

        return Inertia::render('Feed/Index', [
            'feeds' => $feeds,
        ]);
    }


    /**
     * Display add new feed form
     *
     * @return \Inertia\Response
     */
    public function add(Request $request)
    {

        if ($request->isMethod('post')) {
            
            $feed = Feed::create($request->input());
            
            return Redirect::route('feed.index');
        }
        $providers = Provider::all();
        return Inertia::render('Feed/Add', [
            'providers' => $providers,
        ]);
    }
}
