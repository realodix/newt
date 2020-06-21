<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUrl;
use App\Rules\StrAlphaUnderscore;
use App\Rules\StrLowercase;
use App\Rules\URL\KeywordBlacklist;
use App\Url;
use Embed\Embed;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UrlController extends Controller
{
    /**
     * UrlController constructor.
     *
     * @param Url $url
     */
    public function __construct()
    {
        $this->middleware('urlhublinkchecker')->only('create');
    }

    /**
     * Shorten long URLs.
     *
     * @param StoreUrl $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(StoreUrl $request)
    {
        $url = new Url;
        $key = $request->custom_keyword ?? $url->randomKeyGenerator();

        Url::create([
            'user_id'    => Auth::id(),
            'long_url'   => $request->long_url,
            'meta_title' => $request->long_url,
            'keyword'    => $key,
            'is_custom'  => $request->custom_keyword ? 1 : 0,
            'ip'         => $request->ip(),
        ]);

        return redirect()->route('short_url.stats', $key);
    }

    /**
     * Validate the eligibility of a custom keyword that you want to use as a
     * short URL. Response to an AJAX request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customKeywordValidation(Request $request)
    {
        $v = Validator::make($request->all(), [
            'keyword' => [
                'nullable',
                'max:20',
                'unique:urls',
                new StrAlphaUnderscore,
                new StrLowercase,
                new KeywordBlacklist,
            ],
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()->all()]);
        }

        return response()->json(['success' => 'Available']);
    }

    /**
     * @codeCoverageIgnore
     * View the shortened URL details.
     *
     * @param string $key
     * @return \Illuminate\View\View
     */
    public function view($key)
    {
        $url = Url::with('urlStat')->whereKeyword($key)->firstOrFail();

        $qrCode = qrCode($url->short_url);

        try {
            $embed = Embed::create($url->long_url);
        } catch (Exception $error) {
            $embed = null;
        }

        return view('frontend.short', compact(['qrCode']), [
            'embedCode' => $embed->code ?? null,
            'url'       => $url,
        ]);
    }

    /**
     * UrlHub only allows users (registered & unregistered) to have a unique
     * link. You can duplicate it and it will produce a new unique random key.
     *
     * @param string $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate($key)
    {
        $url = new Url;
        $shortenedUrl = Url::whereKeyword($key)->firstOrFail();
        $randomKey = $url->randomKeyGenerator();

        $replicate = $shortenedUrl->replicate()->fill([
            'user_id'   => Auth::id(),
            'keyword'   => $randomKey,
            'is_custom' => 0,
            'clicks'    => 0,
        ]);
        $replicate->save();

        return redirect()->route('short_url.stats', $randomKey)
            ->withFlashSuccess(__('Link was successfully duplicated.'));
    }
}
