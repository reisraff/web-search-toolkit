<?php

declare(strict_types=1);

namespace Search\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Search\Model\Search\Where;

trait RepositoryAware
{
    protected function defaultQuery(
        QueryBuilder $queryBuilder,
        Collection $where,
        Collection $oderBy = null,
        Collection $joins = null,
        Collection $relationships = null,
        array $searchableColumns = [],
        bool $deleted = false
    ): void {
        $getAlias = function ($field) use ($relationships) {
            $field = is_string($field) ? $field : $field->getField();

            if ($relationships) {
                foreach ($relationships as $relationship) {
                    $field = str_replace($relationship->getEntry(), $relationship->getAlias(), $field);
                }
            }

            return $field;
        };

        $referencedFields = [];

        foreach ($where as $condition) {
            if ('search' === $condition->getField()) {
                if (Where::SPECIAL_TYPE === $condition->getType()) {
                    $condition->setType(Where::BOTH_LIKE_TYPE);
                }
                $expressions = [];
                foreach ($searchableColumns as $col) {
                    $expressions[] = $condition->getQueryExpr($queryBuilder, $getAlias($col));
                }
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(...$expressions)
                );
                $where->removeElement($condition);
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

        if ($relationships && $joins) {
            foreach ($referencedFields as $field) {
                foreach ($relationships as $relationship) {
                    if (0 !== strpos($field, $relationship->getEntry())) {
                        continue;
                    }
                    $joins->add($relationship->getEntry());
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
                foreach ($relationships as $relationship) {
                    if ($relationship->getEntry() !== $join) {
                        continue;
                    }
                    $joinMethod = $relationship->getJoinType() . 'Join';
                    $queryBuilder->{$joinMethod}($relationship->getJoinField(), $relationship->getAlias());
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

    abstract protected function isSoftDeletable(): bool;
}
