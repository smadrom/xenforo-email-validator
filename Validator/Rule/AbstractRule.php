<?php
declare(strict_types=1);


namespace SM\EV\Validator\Rule;


use ArrayObject;

/**
 * Class AbstractRule
 * @package SM\EV\Validator\Rule
 */
abstract class AbstractRule
{
    abstract public function __invoke(string $value, ArrayObject $options, callable $next): void;

    protected function splitAndGroupWords(string $option): string
    {
        return $this->groupWordForRegex($this->splitOptionValues($option));
    }

    protected function splitOptionValues(string $values): array
    {
        return preg_split('/\r?\n/', $values);
    }

    protected function groupWordForRegex(array $words): string
    {
        return implode('|', $this->escapeWordForRegexGroup($words));
    }

    protected function escapeWordForRegexGroup(array $words): array
    {
        return array_map('preg_quote', $words);
    }
}