<?php

namespace App\Http\Requests\Concerns;

/**
 * An empty number box arrives as an empty string, which Laravel's
 * ConvertEmptyStringsToNull middleware turns into null. When the column is
 * NOT NULL with a default, that null is rejected by the database and the
 * admin sees an error page instead of a saved record.
 *
 * The column default only applies when a column is left out of the INSERT
 * altogether — sending an explicit null is not the same thing, which is what
 * makes this trip people up.
 */
trait DefaultsSortOrder
{
    /** The order box: empty means "first". */
    protected function defaultSortOrder(): array
    {
        return ['sort_order' => $this->input('sort_order') ?? 0];
    }

    /** Any other number box that writes to a NOT NULL column. */
    protected function zeroFor(array $keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = $this->input($key) ?? 0;
        }

        return $values;
    }
}
