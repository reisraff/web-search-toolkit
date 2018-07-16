<?php

namespace Search\Exception;

class QueryNotFoundException extends \Exception
{
    /**
     * @var string
     */
    private $query;

    public function __construct($query, $code = null)
    {
        $this->$query = $query;

        parent::__construct(
            sprintf(
                'Query "%s" not found',
                $query
            ),
            $code
        );
    }

    /**
     * Gets the value of query.
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }
}