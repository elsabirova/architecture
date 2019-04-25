<?php

declare(strict_types = 1);

namespace Service\BuilderForm;

class Input extends FormEntity
{
    protected $type;
    protected $value;
    protected $placeholder;
    protected $checked;

    /**
     * Input constructor.
     * @param array $params
     */
    public function __construct(array $params) {
        parent::__construct($params);
        $this->type = $params['type'];
        $this->value = $params['value'];
        $this->placeholder = $params['placeholder'];
        $this->checked = $params['checked'];
    }

    /**
     * @return string
     */
    public function render(): string {
        $checked = '';
        if($this->checked) {
            $checked = 'checked';
        }
        return "{$this->title}<input name='{$this->name}' class='{$this->class}' type='{$this->type}' value='{$this->value}' placeholder='{$this->placeholder}' {$checked}><br>";
    }
}