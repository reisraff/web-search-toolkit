<?php

namespace Search\Exception;

class AllResultsDisabledException extends \Exception
{
    public function __construct()
    {
        parent::__construct('All results is disabled for this query');
    }
}