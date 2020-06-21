<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\AllUrlService;
use App\Url;

class AllUrlController extends Controller
{
    /**
     * AllUrlController constructor.
     */
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Show all short URLs created by all users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend.all-url');
    }

    /**
     * @codeCoverageIgnore
     */
    public function dataTable(AllUrlService $allUrlService)
    {
        return $allUrlService->dataTable();
    }

    /**
     * Delete a Short URL on user (Admin) request.
     *
     * @param \App\Url $url
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function delete(Url $url)
    {
        $url->delete();

        return redirect()->back()
                         ->withFlashSuccess(__('Link was successfully deleted.'));
    }
}
