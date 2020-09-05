<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection LongInheritanceChainInspection */
declare(strict_types=1);

namespace SM\EV\XF\Validator;

/**
 * Class Email
 * @package SM\EV\XF\Validator
 */
class Email extends XFCP_Email
{
    /**
     * @param mixed $value
     * @param null|string $errorKey
     * @return bool
     */
    public function isValid($value, &$errorKey = null)
    {
        $isValid = parent::isValid($value, $errorKey);

        if ($isValid) {

            $options = $this->app->options();

            $rules = [
                'DomainList',
                'DisallowedBeforeWords',
                'DisallowedWords',
                'RegexList',
            ];

            foreach ($rules as $rule) {

                $option = $this->getRuleOption($rule, $options);

                if ($option !== null) {

                    if ($rule === 'DomainList') {
                        $rule = ucfirst($options->{'smev' . $rule . 'Mode'}) . 'Domain';
                    }

                    $method = 'is' . $rule;

                    if (method_exists($this, $method) && !$this->{$method}($value, $option)) {
                        $errorKey = 'invalid';
                        return false;
                    }
                }
            }
        }


        return $isValid;
    }

    private function getRuleOption(string $rule, \ArrayObject $options): ?string
    {
        if ((bool)$options->{'smevEnable' . $rule}) {
            $optionKey = 'smev' . $rule;
            return isset($options->{$optionKey}) ? (string)$options->{$optionKey} : null;
        }
        return null;
    }

    private function isDisallowedDomain(string $value, string $option): bool
    {
        $words = $this->splitOptionValues($option);

        if (preg_match('/@(' . $this->groupWordForRegex($words) . ')$/i', $value)) {
            return false;
        }

        return true;
    }

    private function isAllowedDomain(string $value, string $option): bool
    {

        $words = $this->splitOptionValues($option);

        if (!preg_match('/@(' . $this->groupWordForRegex($words) . ')$/i', $value)) {
            return false;
        }

        return true;
    }

    private function isDisallowedBeforeWords(string $value, string $option): bool
    {
        $words = $this->splitOptionValues($option);

        if (preg_match('/(' . $this->groupWordForRegex($words) . ').*@/i', $value, $matches)) {
            return false;
        }

        return true;
    }

    private function isDisallowedWords(string $value, string $option): bool
    {
        $words = $this->splitOptionValues($option);

        if (preg_match('/(' . $this->groupWordForRegex($words) . ')/i', $value, $matches)) {
            return false;
        }

        return true;
    }

    private function isRegexList(string $value, string $option): bool
    {
        foreach ($this->splitOptionValues($option) as $regex) {
            if (preg_match($regex, $value)) {
                return false;
            }
        }
        return true;
    }

    private function splitOptionValues(string $values): array
    {
        return preg_split('/\r?\n/', $values);
    }

    private function groupWordForRegex(array $words): string
    {
        return implode('|', $this->escapeWordForRegexGroup($words));
    }

    private function escapeWordForRegexGroup(array $words): array
    {
        return array_map('preg_quote', $words);
    }
}