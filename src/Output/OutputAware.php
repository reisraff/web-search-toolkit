<?php

declare(strict_types=1);

namespace Search\Output;

use League\Fractal\TransformerAbstract;

trait OutputAware
{
    /**
     * @var outputs
     */
    private $outputs;

    public function addOutput(string $name, TransformerAbstract $output)
    {
        $this->outputs[$name] = $output;
    }

    public function getOutput(string $name): ? TransformerAbstract
    {
        return isset($this->outputs[$name]) ? $this->outputs[$name] : null;
    }
}
