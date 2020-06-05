<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Url;
use App\User;

class StatisticsController extends Controller
{
    /**
     * StatisticsController constructor.
     */
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Show users all their Short URLs.
     */
    public function view()
    {
        $url = new Url;
        $user = new User;

        return view('backend.statistics', [
            'capacity'             => $url->keyword_capacity(),
            'remaining'            => $url->keyword_remaining(),
            'totalShortUrl'        => $url->totalShortUrl(),
            'totalShortUrlByGuest' => $url->totalShortUrlById(),
            'totalClicks'          => $url->totalClicks(),
            'totalClicksByGuest'   => $url->totalClicksById(),
            'totalUser'            => $user->totalUser(),
            'totalGuest'           => $user->totalGuest(),
        ]);
    }
}
