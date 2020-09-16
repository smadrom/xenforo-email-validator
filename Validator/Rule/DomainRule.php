<?php
declare(strict_types=1);

namespace SM\EV\Validator\Rule;

use ArrayObject;
use SM\EV\Exception\InvalidEmailException;

/**
 * Class DisallowedDomain
 * @package SM\EV\Validator
 */
class DomainRule extends AbstractRule
{
    public const ENABLE = 'smevEnableDomainList';
    public const VALUES = 'smevDomainList';
    public const MODE = 'smevDomainListMode';

    public function __invoke(string $value, ArrayObject $options, callable $next): void
    {
        $values = $options[self::VALUES];

        $result = preg_match('/@(?<domain>' . $this->splitAndGroupWords($values) . ')$/i', $value, $matches);

        $mode = $options[self::MODE];

        if ($mode === 'disallowed' && $result) {
            throw new InvalidEmailException('smev_disallowed_domain_error', [
                'domain' => $matches['domain'] ?? '',
            ]);
        }

        if ($mode === 'allowed' && !$result) {
            throw new InvalidEmailException('smev_allowed_domain_error', [
                'domains' => implode(', ', $this->splitOptionValues($values)),
            ]);
        }

        $next($value, $options, $next);
    }
}