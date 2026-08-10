<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QueryOptions
{
    public function apply(
        Builder $query,
        array $options,
        array $searchColumns = [],
        array $filterColumns = [],
        array $sortableColumns = ['id'],
        array $relationSearch = []
    ): Builder {
        $search = trim((string) ($options['s'] ?? ''));

        if ($search !== '') {
            $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

            $query->where(function (Builder $searchQuery) use (
                $search,
                $operator,
                $searchColumns,
                $relationSearch
            ) {
                foreach ($searchColumns as $column) {
                    $searchQuery->orWhere($column, $operator, "%{$search}%");
                }

                foreach ($relationSearch as $relation => $columns) {
                    $searchQuery->orWhereHas($relation, function (Builder $relationQuery) use (
                        $columns,
                        $operator,
                        $search
                    ) {
                        $relationQuery->where(function (Builder $nested) use ($columns, $operator, $search) {
                            foreach ($columns as $column) {
                                $nested->orWhere($column, $operator, "%{$search}%");
                            }
                        });
                    });
                }
            });
        }

        foreach ($filterColumns as $requestKey => $column) {
            $requestKey = is_int($requestKey) ? $column : $requestKey;

            if (array_key_exists($requestKey, $options) && $options[$requestKey] !== null) {
                $query->where($column, $options[$requestKey]);
            }
        }

        $sort = (string) ($options['sort'] ?? 'id');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        if (! in_array($column, $sortableColumns, true)) {
            $column = 'id';
            $direction = 'asc';
        }

        return $query->orderBy($column, $direction);
    }

    public function result(Builder $query, array $options)
    {
        if (array_key_exists('pagination', $options)
            && ! filter_var($options['pagination'], FILTER_VALIDATE_BOOLEAN)) {
            return $query->get();
        }

        $rows = min(
            (int) ($options['rows'] ?? config('erp.default_page_size')),
            (int) config('erp.maximum_page_size')
        );

        return $query->paginate($rows)->appends(request()->query());
    }
}
