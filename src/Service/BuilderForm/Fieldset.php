<?php

declare(strict_types = 1);

namespace Service\BuilderForm;

class Fieldset extends FormComposite
{
    public function render(): string {
        $children = parent::render();
        return "<fieldset class='{$this->class}'><legend>{$this->title}</legend>{$children}</fieldset>";
    }
}