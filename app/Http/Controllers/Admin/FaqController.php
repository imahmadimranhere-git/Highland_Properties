<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;

class FaqController extends Controller
{
    public function store(FaqRequest $request): RedirectResponse
    {
        Faq::create($request->validated());

        return back()->with('success', 'Question added.');
    }

    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        return back()->with('success', 'Question updated.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('success', 'Question removed.');
    }
}
