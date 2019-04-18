<?php

namespace Service\Notification;

use Model\Entity\User;
use Service\Notification\NotificationTypes\INotification;

class NotificationContext
{
    /**
     * @var INotification $notification
     */
    protected $notification;

    /**
     * @param INotification $notification
     */
    public function __construct(INotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @param User $user
     * @param string $templateName
     * @param array $params
     * @throws Exception\NotificationException
     */
    public function notify(User $user, string $templateName, array $params = [])
    {
        $this->notification->process($user, $templateName, $params);
    }
}