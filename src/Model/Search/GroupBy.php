<?php

declare(strict_types=1);

namespace Search\Model\Search;

class GroupBy
{
    /**
     * @var string
     */
    private $field;

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
}
