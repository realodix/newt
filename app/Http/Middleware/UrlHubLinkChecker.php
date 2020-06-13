<?php

namespace App\Http\Middleware;

use App\Url;
use Closure;
use Illuminate\Support\Facades\Auth;

class UrlHubLinkChecker
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $url = new Url();
        $longUrl = rtrim($request->long_url, '/');

        /*
        |----------------------------------------------------------------------
        | Remaining Keyword
        |----------------------------------------------------------------------
        |
        | Periksa apakah UrlHub masih memiliki keyword yang tersedia untuk
        | membuat URL pendek. Jika tidak tersedia, cegah membuat URL
        | pendek.
        |
        */

        if ($url->keyword_remaining() == 0) {
            return redirect()
                   ->back()
                   ->withFlashError(
                       __('Sorry, our service is currently under maintenance.')
                   );
        }

        /*
        |----------------------------------------------------------------------
        | Long Url Exists
        |----------------------------------------------------------------------
        |
        | Check if a long URL already exists in the database. If found,
        | display a warning.
        |
        */

        if (Auth::check()) {
            $s_url = Url::whereUserId(Auth::id())
                          ->whereLongUrl($longUrl)
                          ->first();
        } else {
            $s_url = Url::whereLongUrl($longUrl)
                          ->whereNull('user_id')
                          ->first();
        }

        if ($s_url) {
            return redirect()->route('short_url.stats', $s_url->keyword)
                             ->with('msgLinkAlreadyExists', __('Link already exists.'));
        }

        return $next($request);
    }
}
