<?php

declare(strict_types=1);

namespace Search\Output;

use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\JsonResponse;

trait FractalAware
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var array
     */
    private $defaultIncludes = [];

    public function createItemResponse(
        $item,
        TransformerAbstract $transformer,
        array $fields,
        int $code = JsonResponse::HTTP_OK
    ): JsonResponse {
        $this->parseOutput($fields);

        $resource = new Item($item, $transformer, 'data');
        $array = $this->manager->createData($resource)->toArray();

        return new JsonResponse($array, $code);
    }

    public function createCollectionResponse(
        $collection,
        TransformerAbstract $transformer,
        array $fields,
        $code = JsonResponse::HTTP_OK
    ): JsonResponse {
        $this->parseOutput($fields);

        $resource = new Collection($collection, $transformer, 'data');
        $array = $this->manager->createData($resource)->toArray();

        return new JsonResponse($array, $code);
    }

    public function createCollectionResponseFromPaginator(
        Pagerfanta $paginator,
        TransformerAbstract $transformer,
        array $fields,
        int $code = JsonResponse::HTTP_OK
    ): JsonResponse {
        $this->parseOutput($fields);

        $paginatorAdapter = new PagerfantaPaginatorAdapter(
            $paginator,
            function () {
                return false;
            } // Route generator
        );

        $resource = new Collection($paginator->getCurrentPageResults(), $transformer, 'data');
        $resource->setPaginator($paginatorAdapter);
        $array = $this->manager->createData($resource)->toArray();

        if (isset($array['meta']['pagination'])) {
            $paginationData = $array['meta']['pagination'];
            $paginationData['perPage'] = $paginationData['per_page'];
            $paginationData['currentPage'] = $paginationData['current_page'];
            $paginationData['totalPages'] = $paginationData['total_pages'];
            $paginationData['totalRegisters'] = $paginationData['total'];
            $paginationData['registersReturned'] = $paginationData['count'];
            unset(
                $paginationData['per_page'],
                $paginationData['current_page'],
                $paginationData['total_pages'],
                $paginationData['links'],
                $paginationData['total'],
                $paginationData['count']
            );
            $array['meta']['pagination'] = $paginationData;
        }

        return new JsonResponse($array, $code);
    }

    private function parseOutput(array $fields)
    {
        if (!count($fields)) {
            if (count($this->defaultIncludes)) {
                $this->getFractalManager()->parseIncludes(implode(',', $this->defaultIncludes));
            }
        } else {
            $newFields = [];
            foreach ($fields as $key => $value) {
                $value = explode(',', $value);
                if ('self' === $key) {
                    $newFields['data'] = $value;
                } else {
                    $newFields[substr($key, strpos($key, '.') + 1)] = $value;
                }
            }
            $includes = array_keys($newFields);

            $updateArray = function (array &$array, $key, $value) {
                $value = is_array($value) ? $value : [$value];
                if (array_key_exists($key, $array)) {
                    $array[$key] = array_unique(array_merge($array[$key], $value));
                } else {
                    $array[$key] = $value;
                }
            };

            do {
                $oneMoreTime = false;
                foreach ($includes as $include) {
                    $exploded = explode('.', $include);
                    if (1 < count($exploded)) {
                        array_pop($exploded);
                        $shouldContain = implode('.', $exploded);
                        if (!in_array($shouldContain, $includes)) {
                            $includes[] = $shouldContain;
                            $updateArray($newFields, $shouldContain, []);
                            $oneMoreTime = true;
                        }
                    }
                }
            } while ($oneMoreTime);

            $finalFields = [];

            foreach ($newFields as $key => $value) {
                if ('data' === $key) {
                    $updateArray($finalFields, 'data', $value);
                    continue;
                }

                $x = explode('.', $key);
                if (1 === count($x)) {
                    $updateArray($finalFields, $x[0], $value);
                    $updateArray($finalFields, 'data', $x[0]);
                } else {
                    $lKey = $x[count($x) - 1];
                    $key = $x[count($x) - 2];
                    if (isset($x[count($x) - 3])) {
                        $last = $x[count($x) - 3];
                    } else {
                        $last = 'data';
                    }
                    $updateArray($finalFields, $lKey, $value);
                    $updateArray($finalFields, $key, $lKey);
                    $updateArray($finalFields, $last, $key);
                }
            }

            $finalFields = array_map(
                function ($val) {
                    return implode(',', $val);
                },
                $finalFields
            );

            $this->getFractalManager()->parseIncludes(implode(',', $includes));
            $this->getFractalManager()->parseFieldsets($finalFields);
        }
    }

    /**
     * Sets the value of manager.
     *
     * @param Manager $manager the manager
     */
    public function setFractalManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Gets the value of manager.
     *
     * @return Manager
     */
    public function getFractalManager(): Manager
    {
        return $this->manager;
    }

    /**
     * Gets the value of defaultIncludes.
     *
     * @return array
     */
    public function getDefaultIncludes(): array
    {
        return $this->defaultIncludes;
    }

    /**
     * Sets the value of defaultIncludes.
     *
     * @param array $defaultIncludes the default includes
     *
     * @return self
     */
    public function setDefaultIncludes(array $defaultIncludes): self
    {
        $this->defaultIncludes = $defaultIncludes;

        return $this;
    }
}
