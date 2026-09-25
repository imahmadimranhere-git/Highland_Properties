<?php

namespace App\Http\Requests\Concerns;

/**
 * An empty "Order" box arrives as an empty string, which Laravel's
 * ConvertEmptyStringsToNull middleware turns into null — and the column is
 * NOT NULL, so the insert fails.
 *
 * Every form that has an order field uses this, so leaving the box empty
 * simply means "first / unordered" instead of an error page.
 */
trait DefaultsSortOrder
{
    protected function defaultSortOrder(): array
    {
        return ['sort_order' => $this->input('sort_order') ?? 0];
    }
}
