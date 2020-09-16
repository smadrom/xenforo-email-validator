<?php
declare(strict_types=1);

namespace SM\EV\Validator\Rule;

use ArrayObject;
use SM\EV\Exception\InvalidEmailException;

/**
 * Class BeforeWordRule
 * @package SM\EV\Validator
 */
class BeforeWordRule extends AbstractRule
{
    public const ENABLE = 'smevEnableDisallowedBeforeWords';
    public const VALUES = 'smevDisallowedBeforeWords';

    public function __invoke(string $value, ArrayObject $options, callable $next): void
    {
        if (preg_match('/(?<word>' .$this->splitAndGroupWords($options[self::VALUES]) . ').*@/i', $value, $matches)) {
            throw new InvalidEmailException('smev_word_error', [
                'word' => $matches['word'] ?? '',
            ]);
        }

        $next($value, $options, $next);
    }
}