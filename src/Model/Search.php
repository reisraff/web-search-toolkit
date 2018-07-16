<?php

declare(strict_types=1);

namespace Search\Model;

use Search\Model\Search\GroupBy;
use Search\Model\Search\OrderBy;
use Search\Model\Search\Where;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Search
{
    /**
     * @var int
     */
    private $page = 1;
    /**
     * @var int
     */
    private $perPage = 10;

    /**
     * @var iterable
     */
    private $where;

    /**
     * @var iterable
     */
    private $orderBy;

    /**
     * @var iterable
     */
    private $groupBy;

    /**
     * @var iterable
     */
    private $joins;

    public function __construct()
    {
        $this->where = new ArrayCollection();
        $this->orderBy = new ArrayCollection();
        $this->groupBy = new ArrayCollection();
        $this->joins = new ArrayCollection();
    }

    /**
     * Gets the value of page.
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Sets the value of page.
     *
     * @param int $page the page
     *
     * @return self
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Gets the value of perPage.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Sets the value of perPage.
     *
     * @param int $perPage the per page
     *
     * @return self
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Gets the value of where.
     *
     * @return iterable
     */
    public function getWhere(): Collection
    {
        return $this->where;
    }

    /**
     * Sets the value of where.
     *
     * @param iterable $where the where
     *
     * @return self
     */
    public function setWhere(iterable $where): self
    {
        $where = is_array($where) ? $where : iterator_to_array($where, false);
        $this->where = new ArrayCollection($where);

        return $this;
    }

    /**
     * Gets the value of orderBy.
     *
     * @return iterable
     */
    public function getOrderBy(): Collection
    {
        return $this->orderBy;
    }

    /**
     * Sets the value of orderBy.
     *
     * @param iterable $orderBy the order by
     *
     * @return self
     */
    public function setOrderBy(iterable $orderBy): self
    {
        $orderBy = is_array($orderBy) ? $orderBy : iterator_to_array($orderBy, false);
        $this->orderBy = new ArrayCollection($orderBy);

        return $this;
    }

    /**
     * Gets the value of groupBy.
     *
     * @return iterable
     */
    public function getGroupBy(): Collection
    {
        return $this->groupBy;
    }

    /**
     * Sets the value of groupBy.
     *
     * @param iterable $groupBy the group by
     *
     * @return self
     */
    public function setGroupBy(iterable $groupBy): self
    {
        $groupBy = is_array($groupBy) ? $groupBy : iterator_to_array($groupBy, false);
        $this->groupBy = new ArrayCollection($groupBy);

        return $this;
    }

    /**
     * Gets the value of joins.
     *
     * @return iterable
     */
    public function getJoins(): Collection
    {
        return $this->joins;
    }

    /**
     * Sets the value of joins.
     *
     * @param iterable $joins the group by
     *
     * @return self
     */
    public function setJoins(iterable $joins): self
    {
        $joins = is_array($joins) ? $joins : iterator_to_array($joins, false);
        $this->joins = new ArrayCollection($joins);

        return $this;
    }
}
