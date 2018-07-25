<?php

namespace Shopworks\Git\Review\File;

class CriteriaFormatter
{
    public function formatMany(array $criteria): array
    {
        return \array_map(function ($value) {
            return $this->format($value);
        }, $criteria);
    }

    public function format(string $value): string
    {
        $value = $this->handleWildcard($value);
        $value = $this->handleBackSlash($value);

        return "/${value}/";
    }

    private function handleWildcard(string $value): string
    {
        return \str_replace('/*', '/.*', $value);
    }

    private function handleBackSlash(string $value): string
    {
        return \str_replace('/', '\\/', $value);
    }
}
