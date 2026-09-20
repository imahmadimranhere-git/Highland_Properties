@props([
    'action',
    'confirm' => 'This cannot be undone. Delete it?',
    'label' => 'Delete',
])

{{-- Submits only after the shared confirmation dialog is accepted. --}}
<form method="POST" action="{{ $action }}" data-confirm="{{ $confirm }}" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn-icon btn-icon--danger" aria-label="{{ $label }}">
        <x-ui.icon name="trash" :size="16" />
    </button>
</form>
