<?php
declare(strict_types=1);

namespace SM\EV\Validator\Rule;

use ArrayObject;
use SM\EV\Exception\InvalidEmailException;

/**
 * Class RegexRule
 * @package SM\EV\Validator
 */
class RegexRule extends AbstractRule
{
    public const ENABLE = 'smevEnableRegexList';
    public const VALUES = 'smevRegexList';

    public function __invoke(string $value, ArrayObject $options, callable $next): void
    {
        foreach ($this->splitOptionValues($options[self::VALUES]) as $regex) {
            if (preg_match($regex, $value)) {
                throw new InvalidEmailException('smev_regex_list_error');
            }
        }

        $next($value, $options, $next);
    }
}