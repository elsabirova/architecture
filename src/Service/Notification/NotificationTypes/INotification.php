<?php

declare(strict_types = 1);

namespace Service\Notification\NotificationTypes;

use Model;
use Service\Notification\Exception\NotificationException;

interface INotification
{
    /**
     * Точка входа по формированию и отправке сообщения пользователю
     *
     * @param Model\Entity\User $user
     * @param string $templateName
     * @param array $params
     *
     * @return void
     *
     * @throws NotificationException
     */
    public function process(Model\Entity\User $user, string $templateName, array $params = []): void;
}
