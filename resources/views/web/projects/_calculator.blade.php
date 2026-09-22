{{--
    Installment calculator, plain JavaScript (resources/js/project.js).
    Each category's plan is passed as data attributes — no API request.
--}}
<div class="calculator card u-mt-40" data-calculator>
    <div class="card__body">
        <h3 class="card__title">Installment calculator</h3>
        <p class="text-muted-hp">Pick a category to see what you pay and when. You can adjust the down payment.</p>

        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label" for="calc-category">Category</label>
                <select id="calc-category" class="form-select" data-calc-category>
                    @foreach ($plans as $category)
                        @php $p = $category->paymentPlan; @endphp
                        <option value="{{ $category->id }}"
                                data-price="{{ (float) $category->total_price }}"
                                data-booking="{{ (float) $p->booking_amount }}"
                                data-down="{{ (float) $p->down_payment }}"
                                data-count="{{ $p->installment_count }}"
                                data-installment="{{ (float) $p->installment_amount }}"
                                data-months="{{ $p->installment_frequency->monthsPerInstallment() }}"
                                data-possession="{{ (float) $p->possession_charges }}">
                            {{ $category->name }} — {{ $category->unit_type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label" for="calc-down">Down payment</label>
                <input id="calc-down" type="number" min="0" step="10000" class="form-control" data-calc-down>
                <span class="form-hint">A larger down payment lowers each installment.</span>
            </div>
        </div>

        <div class="calc-result" aria-live="polite">
            <div><span>At booking</span><strong data-out="booking">—</strong></div>
            <div><span>Down payment</span><strong data-out="down">—</strong></div>
            <div><span data-out="label">Installments</span><strong data-out="installment">—</strong></div>
            <div><span>On possession</span><strong data-out="possession">—</strong></div>
            <div class="calc-result__total"><span>Total</span><strong data-out="total">—</strong></div>
        </div>

        <p class="form-hint u-mb-0">An estimate from the published plan. Final figures are confirmed at booking.</p>
    </div>
</div>
