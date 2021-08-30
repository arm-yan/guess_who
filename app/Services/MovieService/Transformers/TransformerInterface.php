<?php

namespace App\Services\MovieService\Transformers;

/*
 * TransformerInterface Interface
 */

interface TransformerInterface
{
    /**
     * @param string $jsonData
     * @param int $count
     * @return array
     */
    public function transformData(string $jsonData, int $count): array;

    /**
     * @param array $data
     * @return array
     */
    public function transformNames(array $data): array;
}
