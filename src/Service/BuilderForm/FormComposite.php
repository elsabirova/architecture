<?php

declare(strict_types=1);

namespace Service\BuilderForm;

abstract class FormComposite extends FormEntity
{
    /**
     * @var FormEntity[]
     */
    protected $children = [];

    public function add(FormEntity $formEntity) {
        $this->children[$formEntity->getKey()] = $formEntity;
        return $this;
    }

    /**
     * @param $key
     * @throws \Exception
     */
    public function remove($key) {
        if (!isset($this->childs[$key])) {
            throw new \Exception("Child doesn't exists");
        }
        unset($this->children[$key]);
    }

    /**
     * @param $key
     * @return FormEntity
     */
    public function getChild($key) {
        return $this->children[$key];
    }

    /**
     * @return FormEntity[]
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @return string
     */
    public function render(): string {
        $result = '';
        foreach ($this->children as $name => $child) {
            $result .= $child->render();
        }
        return $result;
    }
}