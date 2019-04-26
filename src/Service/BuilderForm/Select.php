<?php

declare(strict_types=1);

namespace Service\BuilderForm;

class Select extends FormEntity
{
    protected $values;

    /**
     * Textarea constructor.
     * @param array $params
     */
    public function __construct(array $params) {
        parent::__construct($params);
        $this->values = $params['values'];
    }

    /**
     * @return string
     */
    public function render(): string {
        $result = "{$this->title}<select name='{$this->name}' class='{$this->class}'>";
        foreach ($this->values as $key => $option) {
            $result .= "<option value='{$key}'> {$option}</option>";
        }
        return $result . "</select><br>";
    }
}