<?php

namespace Service\BuilderForm;

abstract class FormEntity
{
    protected $name;
    protected $key;
    protected $title;
    protected $class;

    /**
     * FormEntity constructor.
     *
     * @param array $params
     */
    public function __construct(array $params) {
        $this->key = $params['key'];
        $this->name = $params['name'];
        $this->title = $params['title'];
        $this->class = $params['class'];
    }

    /**
     * @return string
     */
    public function getKey(): string {
        return $this->key;
    }

    abstract public function render(): string;
}