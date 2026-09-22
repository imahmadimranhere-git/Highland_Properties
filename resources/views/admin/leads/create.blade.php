@extends('layouts.admin')

@section('title', 'Add lead')

@section('content')
    <x-panel.page-head title="Add lead" sub="For phone calls, walk-ins and WhatsApp inquiries." />

    <x-panel.box>
        <form method="POST" action="{{ route('admin.leads.store') }}" novalidate>
            @csrf

            @if ($errors->any())
                <div class="alert alert--danger">Please correct the fields marked below.</div>
            @endif

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label" for="name">Client name <span class="required">*</span></label>
                    <input id="name" name="name" type="text" maxlength="150" required
                           class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-3 form-group">
                    <label class="form-label" for="phone">Phone <span class="required">*</span></label>
                    <input id="phone" name="phone" type="tel" maxlength="30" required
                           class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-3 form-group">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" maxlength="150"
                           class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-4 form-group">
                    <label class="form-label" for="project_id">Project</label>
                    <select id="project_id" name="project_id" class="form-select">
                        <option value="">General inquiry</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group">
                    <label class="form-label" for="unit_category_id">Category of interest</label>
                    <select id="unit_category_id" name="unit_category_id"
                            class="form-select @error('unit_category_id') is-invalid @enderror">
                        <option value="">Not specified</option>
                        @foreach ($projects as $project)
                            @foreach ($project->unitCategories as $category)
                                <option value="{{ $category->id }}" data-project="{{ $project->id }}"
                                        @selected(old('unit_category_id') == $category->id)>
                                    {{ $category->name }} — {{ $category->unit_type }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                    @error('unit_category_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-4 form-group">
                    <label class="form-label" for="source">Source</label>
                    <select id="source" name="source" class="form-select">
                        @foreach ($sources as $source)
                            <option value="{{ $source->value }}" @selected(old('source', 'manual') === $source->value)>{{ $source->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group">
                    <label class="form-label" for="assigned_to">Assign to</label>
                    <select id="assigned_to" name="assigned_to" class="form-select">
                        <option value="">Unassigned</option>
                        @foreach ($consultants as $consultant)
                            <option value="{{ $consultant->id }}" @selected(old('assigned_to') == $consultant->id)>{{ $consultant->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group">
                    <label class="form-label" for="next_follow_up_at">First follow-up</label>
                    <input id="next_follow_up_at" name="next_follow_up_at" type="date" class="form-control"
                           value="{{ old('next_follow_up_at') }}" min="{{ today()->toDateString() }}">
                </div>

                <div class="col-12 form-group">
                    <label class="form-label" for="message">What they asked about</label>
                    <textarea id="message" name="message" rows="3" maxlength="3000" class="form-control">{{ old('message') }}</textarea>
                </div>
            </div>

            <div class="u-flex u-gap-8">
                <button type="submit" class="btn btn--primary">Create lead</button>
                <a href="{{ route('admin.leads.index') }}" class="btn btn--secondary">Cancel</a>
            </div>
        </form>
    </x-panel.box>
@endsection

@push('scripts')
<script>
    // Category list follows the chosen project.
    (function () {
        const project = document.getElementById('project_id');
        const category = document.getElementById('unit_category_id');
        const options = Array.from(category.querySelectorAll('option[data-project]'));

        function filter() {
            options.forEach((o) => {
                const match = o.dataset.project === project.value;
                o.hidden = !match;
                if (!match && o.selected) category.value = '';
            });
        }

        project.addEventListener('change', filter);
        filter();
    })();
</script>
@endpush
