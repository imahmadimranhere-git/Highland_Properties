<x-panel.box title="Add a question">
    <form method="POST" action="{{ route('admin.faqs.store') }}" novalidate>
        @csrf
        <div class="row">
            <div class="col-md-8 form-group">
                <label class="form-label" for="faq-q">Question <span class="required">*</span></label>
                <input id="faq-q" name="question" type="text" maxlength="255" required
                       class="form-control @error('question') is-invalid @enderror" value="{{ old('question') }}">
                @error('question')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-2 form-group">
                <label class="form-label" for="faq-group">Group</label>
                <input id="faq-group" name="group" type="text" maxlength="60" class="form-control" value="{{ old('group', 'general') }}">
            </div>
            <div class="col-md-2 form-group">
                <label class="form-label" for="faq-order">Order</label>
                <input id="faq-order" name="sort_order" type="number" min="0" max="999" class="form-control" value="{{ old('sort_order', 0) }}">
            </div>
            <div class="col-12 form-group">
                <label class="form-label" for="faq-a">Answer <span class="required">*</span></label>
                <textarea id="faq-a" name="answer" rows="3" maxlength="3000" required
                          class="form-control @error('answer') is-invalid @enderror">{{ old('answer') }}</textarea>
                @error('answer')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="u-flex u-gap-16" style="align-items:center;">
            <div class="form-check u-mb-0">
                <input id="faq-active" name="is_active" type="checkbox" value="1" checked>
                <label for="faq-active">Show on the website</label>
            </div>
            <button class="btn btn--primary btn--sm">Add question</button>
        </div>
    </form>
</x-panel.box>

@forelse ($faqs as $faq)
    <x-panel.box :title="\Illuminate\Support\Str::limit($faq->question, 70)">
        <x-slot:actions>
            <span class="badge {{ $faq->is_active ? 'badge-success' : 'badge-muted' }}">{{ $faq->is_active ? 'Shown' : 'Hidden' }}</span>
            <x-ui.delete-form :action="route('admin.faqs.destroy', $faq)" confirm="Delete this question?" />
        </x-slot:actions>

        <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" novalidate>
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-8 form-group">
                    <input name="question" type="text" maxlength="255" class="form-control" value="{{ $faq->question }}" aria-label="Question">
                </div>
                <div class="col-md-2 form-group">
                    <input name="group" type="text" maxlength="60" class="form-control" value="{{ $faq->group }}" aria-label="Group">
                </div>
                <div class="col-md-2 form-group">
                    <input name="sort_order" type="number" min="0" max="999" class="form-control" value="{{ $faq->sort_order }}" aria-label="Order">
                </div>
                <div class="col-12 form-group">
                    <textarea name="answer" rows="2" maxlength="3000" class="form-control" aria-label="Answer">{{ $faq->answer }}</textarea>
                </div>
            </div>
            <div class="u-flex u-gap-16" style="align-items:center;">
                <div class="form-check u-mb-0">
                    <input id="faq-{{ $faq->id }}" name="is_active" type="checkbox" value="1" @checked($faq->is_active)>
                    <label for="faq-{{ $faq->id }}">Shown</label>
                </div>
                <button class="btn btn--primary btn--sm">Save</button>
            </div>
        </form>
    </x-panel.box>
@empty
    <x-panel.box><x-ui.empty-state title="No questions yet" /></x-panel.box>
@endforelse

{{ $faqs->links() }}
