<?php

declare(strict_types=1);

namespace Search\Input;

use Linio\Component\Input\InputHandler;

trait InputHandlerAware
{
    /**
     * @var InputHandler[]
     */
    protected $inputHandlers;

    public function addInputHandler(string $name, InputHandler $inputHandler)
    {
        $this->inputHandlers[$name] = $inputHandler;
    }

    public function getInputHandler(string $name): ? InputHandler
    {
        return isset($this->inputHandlers[$name]) ? $this->inputHandlers[$name] : null;
    }
}
