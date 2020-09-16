<?php
declare(strict_types=1);

namespace SM\EV\Validator;

use ArrayObject;
use DomainException;
use SM\EV\Validator\Rule\BeforeWordRule;
use SM\EV\Validator\Rule\DomainRule;
use SM\EV\Validator\Rule\RegexRule;
use SM\EV\Validator\Rule\WordRule;
use SplQueue;

/**
 * Class Email
 * @package SM\EV\Validator
 */
class Email
{
    /**
     * @var SplQueue
     */
    private $rules;
    /**
     * @var ArrayObject
     */
    private $options;

    public function __construct(ArrayObject $options)
    {
        $this->options = $options;
        $this->rules = new SplQueue();
    }

    public function isValid(string $email, ?string &$errorMessage): void
    {
        $this->addRules([
            DomainRule::class,
            BeforeWordRule::class,
            WordRule::class,
            RegexRule::class,
        ]);

        try {
            $this->handler($email);
        } catch (DomainException $domainException) {
            $errorMessage = $domainException->getMessage();
        }
    }

    private function handler(string $email): void
    {
        if ($this->rules->isEmpty()) {
            return;
        }

        $current = $this->rules->dequeue();

        if ($this->isEnabledRule($current::ENABLE)) {
            (new $current())($email, $this->options, function () use ($email) {
                $this->handler($email);
            });
        } else {
            $this->handler($email);
        }
    }

    private function isEnabledRule(string $key): bool
    {
        return (bool)$this->options[$key];
    }

    private function addRules(array $rules): void
    {
        foreach ($rules as $rule) {
            $this->rules->enqueue($rule);
        }
    }
}