<?php

declare(strict_types=1);

namespace Search\Output;

use League\Fractal\Serializer\ArraySerializer;

class FractalSerializer extends ArraySerializer
{
    /**
     * {@inheritdoc}
     */
    public function collection($resourceKey, array $data)
    {
        if ('data' === $resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function item($resourceKey, array $data)
    {
        if ('data' === $resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }
}
