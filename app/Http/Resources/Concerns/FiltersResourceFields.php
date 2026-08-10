<?php

namespace App\Http\Resources\Concerns;

trait FiltersResourceFields
{
    protected function filterFields(array $data): array
    {
        $only = $this->requestedFields('only');
        $except = $this->requestedFields('except');

        if ($only !== []) {
            $data = array_intersect_key($data, array_flip($only));
        }

        if ($except !== []) {
            $data = array_diff_key($data, array_flip($except));
        }

        return $data;
    }

    private function requestedFields(string $key): array
    {
        $value = trim((string) request()->query($key, ''));

        if ($value === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }
}
