<?php

namespace App\Services\MovieService\Transformers;

class ActorTransformer implements TransformerInterface
{
    public function transformData(string $jsonData, int $count): array
    {
        $return = [];
        $actors = json_decode($jsonData, true);

        if(!key_exists('results', $actors)) {
            return [];
        }

        foreach ($actors['results'] as $actor) {
            $return[$actor['id']] = [
                'name'      => $actor['name'],
                'imagePath' => $actor['profile_path']
            ];

            if(count($return) == $count) {
                break;
            }
        }

        return $return;
    }

    public function transformNames(array $data): array
    {
        $names = [];

        foreach ($data as $key=>$actor) {
            $names[$key] = $actor['name'];
        }

        return $names;
    }
}
