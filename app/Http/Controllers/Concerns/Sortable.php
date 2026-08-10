<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait Sortable
{
    /**
     * Order a query by the `sort`/`direction` query-string params, restricted
     * to a whitelist of columns so requests can't sort by arbitrary/unindexed
     * or relation columns.
     *
     * @param  array<int, string>  $sortable  columns allowed to be sorted by
     */
    protected function applySort(Builder $query, array $sortable, string $defaultColumn, string $defaultDirection = 'asc'): Builder
    {
        $column = request()->string('sort')->value();
        $direction = request()->string('direction')->value() === 'desc' ? 'desc' : 'asc';

        if (! in_array($column, $sortable, true)) {
            $column = $defaultColumn;
            $direction = $defaultDirection;
        }

        return $query->orderBy($column, $direction);
    }

    /**
     * Sort an already-loaded collection (e.g. an eager-loaded relation shown
     * as a nested table on a "show" page) by the `sort`/`direction`
     * query-string params, in memory.
     *
     * @param  array<string, string|callable>  $sortable  map of allowed sort
     *                                                    keys to either an attribute name or a value-extracting callback
     */
    protected function sortCollection(Collection $collection, array $sortable, string $defaultColumn, string $defaultDirection = 'asc'): Collection
    {
        $column = request()->string('sort')->value();
        $direction = request()->string('direction')->value() === 'desc' ? 'desc' : 'asc';

        if (! array_key_exists($column, $sortable)) {
            $column = $defaultColumn;
            $direction = $defaultDirection;
        }

        $accessor = $sortable[$column];

        return $collection
            ->sortBy($accessor, SORT_REGULAR, $direction === 'desc')
            ->values();
    }
}
