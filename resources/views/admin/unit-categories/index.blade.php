@extends('layouts.admin')

@section('title', 'Unit categories')

@section('content')
    <x-panel.page-head
        :title="$project->name"
        sub="Unit categories and the payment plan attached to each one.">
        <x-slot:actions>
            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn--secondary btn--sm">Edit project</a>
            <a href="{{ route('admin.projects.updates.index', $project) }}" class="btn btn--secondary btn--sm">Development updates</a>
        </x-slot:actions>
    </x-panel.page-head>

    @if ($errors->any())
        <div class="alert alert--danger">
            <strong>Could not save.</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Add form ------------------------------------------------------- --}}
    <x-panel.box title="Add a category">
        <form method="POST" action="{{ route('admin.projects.categories.store', $project) }}" novalidate>
            @include('admin.unit-categories._fields', [
                'category' => new \App\Models\UnitCategory(['size_unit' => 'sq ft', 'availability' => 'available']),
                'plan' => new \App\Models\PaymentPlan(['installment_frequency' => 'monthly']),
                'prefix' => 'new',
                'submit' => 'Add category',
            ])
        </form>
    </x-panel.box>

    {{-- Existing categories --------------------------------------------- --}}
    @forelse ($project->unitCategories as $category)
        <x-panel.box :title="$category->name . ' — ' . $category->unit_type">
            <x-slot:actions>
                <x-ui.status-badge :status="$category->availability" />
                <x-ui.delete-form
                    :action="route('admin.projects.categories.destroy', [$project, $category])"
                    :confirm="'Delete ' . $category->name . ' and its payment plan?'" />
            </x-slot:actions>

            <form method="POST" action="{{ route('admin.projects.categories.update', [$project, $category]) }}" novalidate>
                @method('PUT')
                @include('admin.unit-categories._fields', [
                    'category' => $category,
                    'plan' => $category->paymentPlan ?? new \App\Models\PaymentPlan(['installment_frequency' => 'monthly']),
                    'prefix' => 'cat' . $category->id,
                    'submit' => 'Save category',
                ])
            </form>
        </x-panel.box>
    @empty
        <x-panel.box>
            <x-ui.empty-state
                title="No categories yet"
                text="Add Category A above. The website shows these as the unit table and payment plans." />
        </x-panel.box>
    @endforelse
@endsection
