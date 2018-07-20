<?php

declare(strict_types=1);

namespace Search\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;

trait RepositoryAware
{
    protected function defaultQuery(
        QueryBuilder $queryBuilder,
        Collection $where,
        Collection $oderBy = null,
        Collection $joins = null,
        Collection $aliases = null,
        array $searchableColumns = [],
        bool $deleted = false
    ): void {
        $getAlias = function ($field) use ($aliases) {
            $field = is_string($field) ? $field : $field->getField();

            if ($aliases) {
                foreach ($aliases as $alias) {
                    $field = str_replace($alias->getEntry(), $alias->getAlias(), $field);
                }
            }

            return $field;
        };

        $referencedFields = [];

        foreach ($where as $condition) {
            if ('search' === $condition->getField()) {
                foreach ($searchableColumns as $col) {
                    $queryBuilder->orWhere(
                        $condition->getQueryExpr($queryBuilder, $col)
                    );
                }
                continue;
            }

            $queryBuilder->andWhere(
                $condition->getQueryExpr($queryBuilder, $getAlias($condition))
            );

            $referencedFields[] = $condition->getField();
        }

        if ($oderBy) {
            foreach ($oderBy as $entry) {
                $queryBuilder->addOrderBy($getAlias($entry), $entry->getDirection());
                $referencedFields[] = $entry->getField();
            }
        }

        if ($aliases && $joins) {
            foreach ($referencedFields as $field) {
                foreach ($aliases as $alias) {
                    if (0 !== strpos($field, $alias->getEntry())) {
                        continue;
                    }
                    $joins->add($alias->getEntry());
                    break;
                }
            }
        }

        if ($joins) {
            do {
                $onMoreTime = false;
                foreach ($joins as $join) {
                    $exploded = explode('.', $join);
                    if (2 < count($exploded)) {
                        array_pop($exploded);
                        $shouldContain = implode('.', $exploded);

                        if (!$joins->contains($shouldContain)) {
                            $joins->add($shouldContain);
                            $onMoreTime = true;
                        }
                    }
                }
            } while ($onMoreTime);

            $joins = $joins->toArray();
            $joins = array_unique($joins);
            sort($joins);

            foreach ($joins as $join) {
                foreach ($aliases as $alias) {
                    if ($alias->getEntry() !== $join) {
                        continue;
                    }
                    $joinMethod = $alias->getJoinType() . 'Join';
                    $queryBuilder->{$joinMethod}($alias->getJoinField(), $alias->getAlias());
                    break;
                }
            }
        }

        if (false == $deleted) {
            if ($this->isSoftDeletable()) {
                $alias = strtolower(substr($this->getClassName(), strrpos($this->getClassName(), '\\') + 1, 1));
                $queryBuilder->andWhere($queryBuilder->expr()->isNull(sprintf('%s.deletedAt', $alias)));
            }
        }
    }
}
