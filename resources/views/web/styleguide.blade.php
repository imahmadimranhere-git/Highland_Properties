@extends('layouts.public')

@section('meta_title', 'Theme style guide')

@section('content')
    <section class="u-section">
        <div class="u-container">
            <x-ui.section-heading
                label="Internal"
                title="Theme style guide"
                text="Every component in the design system, on one page. Delete this route before launch." />

            <h3 class="u-mt-40">Buttons</h3>
            <div class="u-flex u-gap-16 u-wrap u-mt-16">
                <button class="btn btn--primary">Save changes</button>
                <button class="btn btn--secondary">Cancel</button>
                <button class="btn btn--tertiary">Download brochure</button>
                <button class="btn btn--primary btn--sm">Small</button>
                <button class="btn btn--primary" disabled>Disabled</button>
            </div>

            <h3 class="u-mt-40">Status badges</h3>
            <div class="u-flex u-gap-8 u-wrap u-mt-16">
                <span class="badge badge-gold">Ongoing</span>
                <span class="badge badge-success">Completed</span>
                <span class="badge badge-navy">Upcoming</span>
                <span class="badge badge-muted">Sold out</span>
                <span class="badge badge-danger">Closed (lost)</span>
            </div>

            <h3 class="u-mt-40">Unit categories table</h3>
            <div class="table-wrap u-mt-16">
                <table class="table-hp">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Unit type</th>
                            <th>Size</th>
                            <th>Availability</th>
                            <th>Total price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Category A</td><td>1 Bed Apartment</td><td>750 sq ft</td>
                            <td><span class="badge badge-success">Available</span></td>
                            <td class="is-price">{{ money(12500000) }}</td>
                        </tr>
                        <tr>
                            <td>Category B</td><td>2 Bed Apartment</td><td>1,150 sq ft</td>
                            <td><span class="badge badge-gold">Limited</span></td>
                            <td class="is-price">{{ money(18900000) }}</td>
                        </tr>
                        <tr>
                            <td>Category C</td><td>3 Bed Corner</td><td>1,620 sq ft</td>
                            <td><span class="badge badge-muted">Sold out</span></td>
                            <td class="is-price">{{ money(26500000) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 class="u-mt-40">Payment plan cards</h3>
            <div class="u-grid u-mt-16">
                @foreach (['Category A' => 1250000, 'Category B' => 1890000] as $cat => $booking)
                    <div class="card card--featured">
                        <div class="card__body">
                            <h4 class="card__title">{{ $cat }}</h4>
                            <p class="card__price-label">Booking amount</p>
                            <p class="card__price u-mb-24">{{ money($booking) }}</p>
                            <p class="text-muted-hp u-mb-0">48 monthly installments &middot; possession charges on handover</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <h3 class="u-mt-40">Inquiry form</h3>
            <div class="card u-mt-16" style="max-width:560px;">
                <div class="card__body">
                    <div class="form-group">
                        <label class="form-label" for="sg-name">Your name <span class="required">*</span></label>
                        <input id="sg-name" type="text" class="form-control" placeholder="Full name">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sg-phone">Phone <span class="required">*</span></label>
                        <input id="sg-phone" type="tel" class="form-control is-invalid" value="03">
                        <span class="form-error">Enter a complete phone number.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sg-cat">Category of interest</label>
                        <select id="sg-cat" class="form-select">
                            <option>Category A — 1 Bed</option>
                            <option>Category B — 2 Bed</option>
                        </select>
                    </div>

                    <button class="btn btn--primary btn--block">Send inquiry</button>
                </div>
            </div>
        </div>
    </section>
@endsection
