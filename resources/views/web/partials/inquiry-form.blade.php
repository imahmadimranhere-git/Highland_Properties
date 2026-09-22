{{-- Project inquiry. Saved as a Lead and assigned to the project's consultant. --}}
@if (session('inquiry_sent'))
    <div class="alert alert--success" role="status">
        <strong>Thank you — your inquiry has reached us.</strong>
        A consultant will call you shortly, usually within one working day.
    </div>
@else
    <form method="POST" action="{{ route('inquiry.store') }}" novalidate>
        @csrf
        <input type="hidden" name="project_id" value="{{ $project->id }}">

        {{-- Honeypot. Invisible to people and screen readers; bots fill it in. --}}
        <div aria-hidden="true" style="position:absolute;left:-9999px;">
            <label for="website">Website</label>
            <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        @if ($errors->any())
            <div class="alert alert--danger">Please check the highlighted fields.</div>
        @endif

        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label" for="inq-name">Your name <span class="required">*</span></label>
                <input id="inq-name" name="name" type="text" maxlength="150" required autocomplete="name"
                       class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label" for="inq-phone">Phone <span class="required">*</span></label>
                <input id="inq-phone" name="phone" type="tel" maxlength="30" required autocomplete="tel" inputmode="tel"
                       class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                @error('phone')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label" for="inq-email">Email</label>
                <input id="inq-email" name="email" type="email" maxlength="150" autocomplete="email"
                       class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label" for="inq-category">Category of interest</label>
                <select id="inq-category" name="unit_category_id" class="form-select @error('unit_category_id') is-invalid @enderror">
                    <option value="">Not sure yet</option>
                    @foreach ($project->unitCategories as $category)
                        <option value="{{ $category->id }}" @selected(old('unit_category_id') == $category->id)>
                            {{ $category->name }} — {{ $category->unit_type }}
                        </option>
                    @endforeach
                </select>
                @error('unit_category_id')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-12 form-group">
                <label class="form-label" for="inq-message">Message</label>
                <textarea id="inq-message" name="message" rows="4" maxlength="2000"
                          class="form-control @error('message') is-invalid @enderror"
                          placeholder="Preferred floor, timing, questions about the plan…">{{ old('message') }}</textarea>
                @error('message')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <button type="submit" class="btn btn--primary btn--lg">Send inquiry</button>
        <p class="form-hint">We only use your number to discuss this project.</p>
    </form>
@endif
