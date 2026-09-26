<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TickerRequest;
use App\Models\Ticker;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TickerController extends Controller
{
    public function index(): View
    {
        return view('admin.tickers.index', [
            'tickers' => Ticker::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(TickerRequest $request): RedirectResponse
    {
        Ticker::create($request->validated());

        return back()->with('success', 'Announcement added.');
    }

    public function update(TickerRequest $request, Ticker $ticker): RedirectResponse
    {
        $ticker->update($request->validated());

        return back()->with('success', 'Announcement updated.');
    }

    public function destroy(Ticker $ticker): RedirectResponse
    {
        $ticker->delete();

        return back()->with('success', 'Announcement removed.');
    }

    /** One click to take a message off the website without deleting it. */
    public function toggle(Ticker $ticker): RedirectResponse
    {
        $ticker->update(['is_active' => ! $ticker->is_active]);

        return back()->with('success', $ticker->is_active ? 'Announcement is live.' : 'Announcement hidden.');
    }
}
