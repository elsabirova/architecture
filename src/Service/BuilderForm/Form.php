<?php

declare(strict_types=1);

namespace Service\BuilderForm;

class Form extends FormComposite
{
    protected $action;
    protected $method;

    public function __construct(array $params) {
        parent::__construct($params);
        $this->action = $params['action'];
        $this->method = $params['method'];
    }

    public function render(): string {
        $children = parent::render();
        return "<h3>{$this->title}</h3><form action='{$this->action}' method='{$this->method}' class='{$this->class}'>{$children}</form>";
    }
}