<?php
declare(strict_types=1);

namespace SM\EV\Exception;

use DomainException;
use Throwable;
use XF;

/**
 * Class InvalidEmailException
 * @package SM\EV\Exception
 */
class InvalidEmailException extends DomainException
{
    /**
     * InvalidEmailException constructor.
     * @param string $name
     * @param array $params
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($name = '', $params = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->getErrorMessage($name, $params), $code, $previous);
    }

    private function getErrorMessage(string $name, array $params): string
    {
        return (string)XF::phrase($name, $params);
    }
}
