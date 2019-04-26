<?php

declare(strict_types=1);

namespace Service\BuilderForm;

class Textarea extends FormEntity
{
    protected $rows;
    protected $cols;

    /**
     * Textarea constructor.
     * @param array $params
     */
    public function __construct(array $params) {
        parent::__construct($params);
        $this->rows = $params['rows'];
        $this->cols = $params['cols'];
    }

    /**
     * @return string
     */
    public function render(): string {
        return "<label for='{$this->name}'>{$this->title}</label><textarea name='{$this->name}' class='{$this->class}' rows='{$this->rows}' cols='{$this->cols}'></textarea> <br>";
    }
}