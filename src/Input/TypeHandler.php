<?php

declare(strict_types=1);

namespace Search\Input;

use Linio\Component\Input\TypeHandler as LinioTypeHander;

class TypeHandler extends LinioTypeHander
{
    public function __construct()
    {
        parent::__construct();

        $this->addType('mixed', Type\Mixed::class);
    }
}
