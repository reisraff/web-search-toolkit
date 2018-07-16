<?php

declare(strict_types=1);

namespace Search\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;

interface SearchInterface
{
    public function search(Collection $where, Collection $orderBy, Collection $joins): AbstractQuery;
}
