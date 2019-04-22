<?php

declare(strict_types=1);

namespace Service\Order;

use Service\Log\Logger;
use Service\User\ISecurity;
use Symfony\Component\HttpFoundation\Request;

class BasketDirector
{
    public function build(Request $request, ISecurity $security) {
        $user = $security->getUser();
        $basketBuilder = new BasketBuilder();
        $basketBuilder->setSession($request->getSession());
        $basketBuilder->setUser($user);
        $basketBuilder->setLogger(new Logger());

        return $basketBuilder->build();
    }
}