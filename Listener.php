<?php
declare(strict_types=1);

namespace SM\EV;

use SM\EV\Validator\Email;
use XF\Entity\User;

/**
 * Class Listener
 * @package SM\EV
 */
class Listener
{
    public static function userEntityPreUpdate(User $entity): void
    {
        if ($entity->isChanged('email') && !$entity->getOption('admin_edit')) {
            $errorMessage = null;

            (new Email($entity->app()->options()))
                ->isValid($entity->email, $errorMessage);

            if ($errorMessage !== null) {
                $entity->error($errorMessage);
                return;
            }
        }
    }
}