<?php

declare(strict_types=1);

namespace Search\Service;

use Search\Exception\QueryNotFoundException;
use Search\Model\Search;
use Search\Repository\SearchInterface;
use Doctrine\Common\Inflector\Inflector;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ServiceAware
{
    public function search(Search $search): Pagerfanta
    {
        $query = $this->getRepository()->search(
            $search->getWhere(),
            $search->getOrderBy(),
            $search->getJoins()
        );

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($query));

        if (0 == $search->getPerPage()) {
            $result = $query->getResult();
            $search->setPage(1);
            $search->setPerPage((count($result) ? count($result) : 1));
        }

        $pagerfanta->setMaxPerPage($search->getPerPage());

        try {
            $pagerfanta->setCurrentPage($search->getPage());
        } catch (NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return $pagerfanta;
    }

    public function query(Search $query, string $queryName): array
    {
        $repository = $this->getRepository();
        $method = sprintf('%sQuery', Inflector::camelize($queryName));

        if (!method_exists($repository, $method)) {
            throw new QueryNotFoundException($queryName);
        }

        return $repository->{$method}($query->getWhere(), $query->getJoins());
    }

    protected function getRepository(): SearchInterface
    {
        $className = get_class($this);
        $method = sprintf(
            'get%sRepository',
            str_replace('Service', null, substr($className, strrpos($className, '\\') + 1))
        );

        return $this->{$method}();
    }
}
