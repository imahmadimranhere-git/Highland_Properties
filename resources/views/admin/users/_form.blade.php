@csrf

@if ($errors->any())
    <div class="alert alert--danger">Please correct the fields marked below.</div>
@endif

@php $isSelf = $user->exists && $user->is(auth()->user()); @endphp

<div class="row">
    <div class="col-md-6 form-group">
        <label class="form-label" for="name">Full name <span class="required">*</span></label>
        <input id="name" name="name" type="text" maxlength="150" required
               class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}">
        @error('name')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-6 form-group">
        <label class="form-label" for="email">Email (used to sign in) <span class="required">*</span></label>
        <input id="email" name="email" type="email" maxlength="150" required autocomplete="off"
               class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
        @error('email')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="phone">Phone</label>
        <input id="phone" name="phone" type="tel" maxlength="30" class="form-control" value="{{ old('phone', $user->phone) }}">
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="designation">Designation</label>
        <input id="designation" name="designation" type="text" maxlength="120" class="form-control"
               value="{{ old('designation', $user->designation) }}">
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="role_id">Role <span class="required">*</span></label>
        <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required @disabled($isSelf)>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" data-slug="{{ $role->slug }}"
                        @selected(old('role_id', $user->role_id) == $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
        @if ($isSelf)
            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
            <span class="form-hint">You cannot change your own role.</span>
        @endif
        @error('role_id')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    @unless ($user->exists)
        <div class="col-md-6 form-group">
            <label class="form-label" for="password">Password <span class="required">*</span></label>
            <input id="password" name="password" type="password" autocomplete="new-password"
                   class="form-control @error('password') is-invalid @enderror">
            <span class="form-hint">At least 8 characters with letters and numbers.</span>
            @error('password')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="col-md-6 form-group">
            <label class="form-label" for="password_confirmation">Confirm password <span class="required">*</span></label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="form-control">
        </div>
    @endunless
</div>

<div id="target-box" class="card card--accent-left u-mb-24">
    <div class="card__body">
        <p class="section-label">Target for {{ now()->format('F Y') }}</p>
        <div class="row">
            <div class="col-md-6 form-group u-mb-0">
                <label class="form-label" for="target_deals">Deals to close</label>
                <input id="target_deals" name="target_deals" type="number" min="0" class="form-control"
                       value="{{ old('target_deals', $target?->target_deals) }}">
            </div>
            <div class="col-md-6 form-group u-mb-0">
                <label class="form-label" for="target_amount">Sales value</label>
                <input id="target_amount" name="target_amount" type="number" step="0.01" min="0" class="form-control"
                       value="{{ old('target_amount', $target?->target_amount) }}">
            </div>
        </div>
    </div>
</div>

@unless ($isSelf)
    <div class="form-check u-mb-24">
        <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $user->is_active ?? true))>
        <label for="is_active">Account is active and can sign in</label>
    </div>
@else
    <input type="hidden" name="is_active" value="1">
@endunless

<div class="u-flex u-gap-8">
    <button type="submit" class="btn btn--primary">{{ $submit }}</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn--secondary">Cancel</a>
</div>

@push('scripts')
<script>
    // Monthly targets only apply to sales consultants.
    (function () {
        const role = document.getElementById('role_id');
        const box = document.getElementById('target-box');
        const sync = () => {
            box.hidden = role.selectedOptions[0]?.dataset.slug !== 'sales_consultant';
        };
        role.addEventListener('change', sync);
        sync();
    })();
</script>
@endpush
