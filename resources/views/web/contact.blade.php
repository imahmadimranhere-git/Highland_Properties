@extends('layouts.public')

@section('meta_title', 'Contact Us | ' . setting('site_name', 'Highland Properties'))

@section('content')
    <x-web.page-hero label="Contact" title="Talk to us" text="Visit the office, call, or leave a message and we will get back to you." />

    <section class="u-section">
        <div class="u-container">
            <div class="row" style="row-gap:40px;">
                <div class="col-lg-5">
                    <ul class="contact-list">
                        @if (setting('phone'))
                            <li><x-ui.icon name="phone" class="icon icon--gold" /><div><span>Phone</span><a href="tel:{{ setting('phone') }}">{{ setting('phone') }}</a></div></li>
                        @endif
                        @if (setting('email'))
                            <li><x-ui.icon name="mail" class="icon icon--gold" /><div><span>Email</span><a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a></div></li>
                        @endif
                        @if (setting('address'))
                            <li><x-ui.icon name="pin" class="icon icon--gold" /><div><span>Office</span>{{ setting('address') }}</div></li>
                        @endif
                    </ul>

                    @php $mapUrl = \App\Support\MapEmbed::url(setting('map_embed_url'), null, null, setting('address')); @endphp

                    <div class="u-mt-24">
                        @include('web.partials.map', ['url' => $mapUrl, 'title' => 'Office location'])
                    </div>
                </div>

                <div class="col-lg-7" id="contact-form">
                    <div class="card card--featured">
                        <div class="card__body">
                            @if (session('contact_sent'))
                                <div class="alert alert--success u-mb-0" role="status">
                                    <strong>Thank you.</strong> Your message has reached us and we will reply soon.
                                </div>
                            @else
                                <form method="POST" action="{{ route('contact.store') }}" novalidate>
                                    @csrf
                                    <div aria-hidden="true" style="position:absolute;left:-9999px;">
                                        <label for="website">Website</label>
                                        <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="c-name">Name <span class="required">*</span></label>
                                            <input id="c-name" name="name" type="text" maxlength="150" required autocomplete="name"
                                                   class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="c-phone">Phone</label>
                                            <input id="c-phone" name="phone" type="tel" maxlength="30" autocomplete="tel"
                                                   class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                            @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="c-email">Email</label>
                                            <input id="c-email" name="email" type="email" maxlength="150" autocomplete="email"
                                                   class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                            @error('email')<span class="form-error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label" for="c-subject">Subject</label>
                                            <input id="c-subject" name="subject" type="text" maxlength="180" class="form-control" value="{{ old('subject') }}">
                                        </div>
                                        <div class="col-12 form-group">
                                            <label class="form-label" for="c-message">Message <span class="required">*</span></label>
                                            <textarea id="c-message" name="message" rows="5" maxlength="3000" required
                                                      class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                                            @error('message')<span class="form-error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <button class="btn btn--primary btn--lg">Send message</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
