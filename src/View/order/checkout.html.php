<?php

/** @var \Service\BuilderForm\Form $orderForm */
/** @var string $response */

$body = function () use ($orderForm, $response) {
    ?>
    <?= $orderForm ?>
    <div><?= $response ?></div>
    <?php
};

$renderLayout(
    'main_template.html.php',
    [
        'title' => 'Покупка',
        'body' => $body,
    ]
);
