<?php
declare(strict_types=1);

namespace SM\EV;

use SM\EV\Validator\Email;
use XF;
use XF\Entity\User;

/**
 * Class Listener
 * @package SM\EV
 */
class Listener
{
    /**
     * @param User $entity
     * @return mixed
     */
    public static function userEntityPreUpdate(User $entity)
    {
        if ($entity->isChanged('email') && !$entity->getOption('admin_edit')) {
            $errorMessage = null;

            (new Email($entity->app()->options()))
                ->isValid($entity->email, $errorMessage);

            if ($errorMessage !== null) {
                return $entity->error($errorMessage);
            }
        }

        die();
    }
}