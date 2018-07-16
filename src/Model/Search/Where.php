<?php

declare(strict_types=1);

namespace Search\Model\Search;

use AppBundle\Source\Controller\Exception\HttpException;
use Doctrine\ORM\QueryBuilder;

class Where
{
    const SPECIAL_TYPE = 'SPECIAL_TYPE';
    const EQUAL_TYPE = 'EQUAL_TYPE';
    const DIFFERENT_TYPE = 'DIFFERENT_TYPE';
    const LEFT_LIKE_TYPE = 'LEFT_LIKE_TYPE';
    const RIGHT_LIKE_TYPE = 'RIGHT_LIKE_TYPE';
    const BOTH_LIKE_TYPE = 'BOTH_LIKE_TYPE';
    const LIKE_TYPE = 'LIKE_TYPE';
    const IS_NULL_TYPE = 'IS_NULL_TYPE';
    const IS_NOT_NULL_TYPE = 'IS_NOT_NULL_TYPE';
    const IN_TYPE = 'IN_TYPE';
    const LESS_TYPE = 'LESS_TYPE';
    const LESS_OR_EQUAL_TYPE = 'LESS_OR_EQUAL_TYPE';
    const GREATER_OR_EQUAL_TYPE = 'GREATER_OR_EQUAL_TYPE';
    const GREATER_TYPE = 'GREATER_TYPE';
    const BETWEEN_TYPE = 'BETWEEN_TYPE';

    public static $whereTypes = [
        self::SPECIAL_TYPE,
        self::EQUAL_TYPE,
        self::DIFFERENT_TYPE,
        self::LEFT_LIKE_TYPE,
        self::RIGHT_LIKE_TYPE,
        self::BOTH_LIKE_TYPE,
        self::LIKE_TYPE,
        self::IS_NULL_TYPE,
        self::IS_NOT_NULL_TYPE,
        self::IN_TYPE,
        self::LESS_TYPE,
        self::LESS_OR_EQUAL_TYPE,
        self::GREATER_OR_EQUAL_TYPE,
        self::GREATER_TYPE,
        self::BETWEEN_TYPE,
    ];

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $query;

    /**
     * Gets the value of field.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Sets the value of field.
     *
     * @param string $field the field
     *
     * @return self
     */
    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Gets the value of type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param string $type the type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the value of query.
     *
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets the value of query.
     *
     * @param mixed $query the query
     *
     * @return self
     */
    public function setQuery($query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getQueryExpr(QueryBuilder $qb, string $colAlias = null)
    {
        $field = $colAlias ?? $this->getField();

        switch ($this->getType()) {
            case self::SPECIAL_TYPE:
                return $qb->expr()->eq($qb->expr()->literal(1), $qb->expr()->literal(1));
                break;
            case self::EQUAL_TYPE:
                return $qb->expr()->eq($field, $qb->expr()->literal($this->getQuery()));
                break;
            case self::DIFFERENT_TYPE:
                return $qb->expr()->neq($field, $qb->expr()->literal($this->getQuery()));
                break;
            case self::LEFT_LIKE_TYPE:
                return $qb->expr()->like($field, $qb->expr()->literal('%' . $this->getQuery()));
                break;
            case self::RIGHT_LIKE_TYPE:
                return $qb->expr()->like($field, $qb->expr()->literal($this->getQuery() . '%'));
                break;
            case self::BOTH_LIKE_TYPE:
                return $qb->expr()->like($field, $qb->expr()->literal('%' . $this->getQuery() . '%'));
                break;
            case self::LIKE_TYPE:
                return $qb->expr()->like($field, $qb->expr()->literal($this->getQuery()));
                break;
            case self::IS_NULL_TYPE:
                return $qb->expr()->isNull($field);
                break;
            case self::IS_NOT_NULL_TYPE:
                return $qb->expr()->isNotNull($field);
                break;
            case self::IN_TYPE:
                return $qb->expr()->in($field, $this->getQuery());
                break;
            case self::LESS_TYPE:
                return $qb->expr()->lt($field, $qb->expr()->literal($this->getQuery()));
                break;
            case self::LESS_OR_EQUAL_TYPE:
                return $qb->expr()->lte($field, $qb->expr()->literal($this->getQuery()));
                break;
            case self::GREATER_OR_EQUAL_TYPE:
                return $qb->expr()->gte($field, $qb->expr()->literal($this->getQuery()));
                break;
            case self::GREATER_TYPE:
                return $qb->expr()->gt($field, $qb->expr()->literal($this->getQuery()));
                break;
            case self::BETWEEN_TYPE:
                $val = $this->getQuery();
                if (!is_array($val) || !isset($val[0]) || !isset($val[1])) {
                    throw new HttpException(
                        'Invalid input',
                        1,
                        sprintf(
                            '[Where::%s] Unexpected BETWEEN_TYPE value',
                            $this->getField()
                        )
                    );
                }

                return $qb->expr()->between($field, $qb->expr()->literal($val[0]), $qb->expr()->literal($val[1]));
                break;
        }
    }
}
