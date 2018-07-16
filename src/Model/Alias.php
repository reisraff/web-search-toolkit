<?php

declare(strict_types=1);

namespace Search\Model;

class Alias
{
    /**
     * @var string
     */
    private $entry;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $joinType;

    /**
     * @var string
     */
    private $joinField;

    public function __construct(string $entry, string $alias, string $joinType, string $joinField = null)
    {
        $this->entry = $entry;
        $this->alias = $alias;
        $this->joinType = $joinType;
        $this->joinField = $joinField;
    }

    /**
     * Gets the value of entry.
     *
     * @return string
     */
    public function getEntry(): string
    {
        return $this->entry;
    }

    /**
     * Sets the value of entry.
     *
     * @param string $entry the entry
     *
     * @return self
     */
    public function setEntry(string $entry): self
    {
        $this->entry = $entry;

        return $this;
    }

    /**
     * Gets the value of alias.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Sets the value of alias.
     *
     * @param string $alias the alias
     *
     * @return self
     */
    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Gets the value of joinType.
     *
     * @return string
     */
    public function getJoinType(): string
    {
        return $this->joinType;
    }

    /**
     * Sets the value of joinType.
     *
     * @param string $joinType the join type
     *
     * @return self
     */
    public function setJoinType(string $joinType): self
    {
        $this->joinType = $joinType;

        return $this;
    }

    /**
     * Gets the value of joinField.
     *
     * @return string
     */
    public function getJoinField(): string
    {
        return null === $this->joinField ? $this->entry : $this->joinField;
    }

    /**
     * Sets the value of joinField.
     *
     * @param string $joinField the join field
     *
     * @return self
     */
    public function setJoinField(string $joinField): self
    {
        $this->joinField = $joinField;

        return $this;
    }
}
