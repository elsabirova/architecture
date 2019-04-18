<?php

namespace Service\Order\Observer;

use Service\Notification\NotificationTypes\Sms;
use Service\Notification\NotificationTypes\Email;
use Service\Notification\NotificationContext;
use SplSubject;

class CheckoutObserver implements \SplObserver
{
    public function update(SplSubject $subject) {
        //Choose a way to notify the user about the purchase
        $notification = new NotificationContext(new Sms());
        //$notification = new NotificationContext(new Email());

        $user = $subject->getUser();

        $notification->notify($user, 'checkout_template');
    }
}