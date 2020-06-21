<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Url;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class DashboardController extends Controller
{
    /**
     * Show users all their Short URLs.
     *
     * @return \Illuminate\View\View
     */
    public function view()
    {
        $url = new Url;
        $user = new User;
        $kwCapacity = $url->keywordCapacity();

        return view('backend.dashboard', [
            'shortUrlCount'        => $url->shortUrlCount(),
            'shortUrlCountByMe'    => $url->shortUrlCountOwnedBy(Auth::id()),
            'shortUrlCountByGuest' => $url->shortUrlCountOwnedBy(),
            'clickCount'           => $url->clickCount(),
            'clickCountFromMe'     => $url->clickCountOwnedBy(Auth::id()),
            'clickCountFromGuest'  => $url->clickCountOwnedBy(),
            'userCount'            => $user->userCount(),
            'guestCount'           => $user->guestCount(),
            'capacity'             => $kwCapacity,
            'remaining'            => $url->keywordRemaining(),
            'remaining_percent'    => remainingPercentage($url->shortUrlCount(), $kwCapacity),
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getData()
    {
        $model = Url::whereUserId(Auth::id());

        return DataTables::of($model)
            ->editColumn('keyword', function ($url) {
                return '<span class="short_url" data-clipboard-text="'.$url->short_url.'" title="'.__('Copy to clipboard').'" data-toggle="tooltip">'.urlRemoveScheme($url->short_url).'</span>';
            })
            ->editColumn('long_url', function ($url) {
                return '
                    <span title="'.$url->meta_title.'" data-toggle="tooltip">'.Str::limit($url->meta_title, 90).'</span>
                    <br>
                    <a href="'.$url->long_url.'" target="_blank" title="'.$url->long_url.'" data-toggle="tooltip" class="text-muted">'.urlLimit($url->long_url, 70).'</a>';
            })
            ->editColumn('clicks', function ($url) {
                return '<span title="'.number_format($url->clicks).' clicks" data-toggle="tooltip">'.numberFormatShort($url->clicks).'</span>';
            })
            ->editColumn('created_at', function ($url) {
                return [
                    'display'   => '<span title="'.$url->created_at->toDayDateTimeString().'" data-toggle="tooltip">'.$url->created_at->diffForHumans().'</span>',
                    'timestamp' => $url->created_at->timestamp,
                ];
            })
            ->addColumn('action', function ($url) {
                return
                    '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                        <a role="button" class="btn" href="'.route('short_url.stats', $url->keyword).'" target="_blank" title="'.__('Details').'" data-toggle="tooltip"><i class="fa fa-eye"></i></a>
                        <a role="button" class="btn" href="'.route('dashboard.duplicate', $url->keyword).'" title="'.__('Duplicate').'" data-toggle="tooltip"><i class="far fa-clone"></i></a>
                        <a role="button" class="btn" href="'.route('short_url.edit', $url->keyword).'" title="'.__('Edit').'" data-toggle="tooltip"><i class="fas fa-edit"></i></a>
                        <a role="button" class="btn" href="'.route('dashboard.delete', $url->getRouteKey()).'" title="'.__('Delete').'" data-toggle="tooltip"><i class="fas fa-trash-alt"></i></a>
                    </div>';
            })
            ->rawColumns(['keyword', 'long_url', 'clicks', 'created_at.display', 'action'])
            ->toJson();
    }

    /**
     * Show the long url edit page.
     *
     * @param string $key
     *
     * @return \Illuminate\View\View
     */
    public function edit($key)
    {
        $url = Url::whereKeyword($key)->firstOrFail();

        $this->authorize('updateUrl', $url);

        return view('backend.edit', compact('url'));
    }

    /**
     * Update the long url that was previously set to the new long url.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Url                 $url
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Url $url)
    {
        $url->long_url = $request->input('long_url');
        $url->meta_title = $request->input('meta_title');
        $url->save();

        return redirect()->route('dashboard')
                         ->withFlashSuccess(__('Link changed successfully !'));
    }

    /**
     * Delete a shortened URL on user request.
     *
     * @param \App\Url $url
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(Url $url)
    {
        $this->authorize('forceDelete', $url);

        $url->delete();

        return redirect()->back()
                         ->withFlashSuccess(__('Link was successfully deleted.'));
    }

    /**
     * UrlHub only allows users (registered & unregistered) to have a unique
     * link. You can duplicate it and it will produce a new unique random key.
     *
     * @param string $key
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function duplicate($key)
    {
        $url = new Url;
        $shortenedUrl = Url::whereKeyword($key)->firstOrFail();

        $replicate = $shortenedUrl->replicate()->fill([
            'user_id'   => Auth::id(),
            'keyword'   => $url->randomKeyGenerator(),
            'is_custom' => 0,
            'clicks'    => 0,
        ]);
        $replicate->save();

        return redirect()->back()
                         ->withFlashSuccess(__('Link was successfully duplicated.'));
    }
}
