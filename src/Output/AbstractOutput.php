<?php

declare(strict_types=1);

namespace Search\Output;

use Search\Output\OutputAware;
use League\Fractal\TransformerAbstract;

abstract class AbstractOutput extends TransformerAbstract
{
    use OutputAware;
}
