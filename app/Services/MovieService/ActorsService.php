<?php

namespace App\Services\MovieService;

use App\Services\MovieService\Transformers\ActorTransformer;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

/**
 *  ActorsService Class
 */
class ActorsService
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var ActorTransformer
     */
    private ActorTransformer $transformer;

    /**
     * @var string|mixed
     */
    private string $apiKey;

    /**
     * @var string
     */
    private string $endpoint = 'https://api.themoviedb.org/3/person';

    /**
     * @var string
     */
    private string $imagePath = 'https://www.themoviedb.org/t/p/w220_and_h330_face';

    /**
     * @param Client $client
     * @param ActorTransformer $transformer
     */
    public function __construct(Client $client, ActorTransformer $transformer)
    {
        $this->client = $client;
        $this->transformer = $transformer;
        $this->apiKey = env('THE_MOVIE_API_KEY');
    }

    /**
     * Get popular actors collection
     * @return array
     * @throws GuzzleException
     */
    public function getPopular(): array
    {
        $uri = $this->getPopularPersonsEndpoint();

        return $this->getDataFromApi($uri);
    }

    /**
     * Parse actors collection as needed
     * @param string $data
     * @param int $count
     * @return array
     */
    public function parseActorsData(string $data, int $count): array
    {
        return $this->transformer->transformData($data, $count);
    }

    /**
     * Grab only the actors names and ids
     * @param array $data
     * @return array
     */
    public function parseActorsNames(array $data): array
    {
        return $this->transformer->transformNames($data);
    }

    /**
     * Get image full path from the api
     * @param string $imagePath
     * @return string
     */
    public function getImageFullPath(string $imagePath): string
    {
        if ($imagePath[0] != '/') {
            $imagePath = '/' . $imagePath;
        }

        return $this->imagePath . $imagePath;
    }

    /**
     * Get actor details by given actor id
     * @param int|null $actorId
     * @return array
     * @throws GuzzleException
     */
    public function getActorDetails(?int $actorId): array
    {
        if(!$actorId) {
            return $this->returnData(['description' => 'Not found'], 404);;
        }

        $uri = $this->getActorDetailsEndpoint($actorId);

        return $this->getDataFromApi($uri);
    }

    /**
     * Make a call to api and return the data if it's available
     * @param string $uri
     * @return array
     * @throws GuzzleException
     */
    private function getDataFromApi(string $uri): array
    {
        try {
            $result = $this->client->get($uri);
            $data = $result->getBody()->getContents();
            $returnData = [
                'data' => $data
            ];

            return $this->returnData($returnData, $result->getStatusCode());
        } catch (\Exception $e) {
            $returnData = [
                'description' => 'Service Unavailable',
                'errorMessage' => $e->getMessage()
            ];

            return $this->returnData($returnData, 503);
        }
    }

    /**
     * Return endpoint for popular persons
     * @return string
     */
    private function getPopularPersonsEndpoint(): string
    {
        return "{$this->endpoint}/popular?api_key={$this->apiKey}";
    }

    /**
     * Return endpoint for actor details
     * @return string
     */
    private function getActorDetailsEndpoint(int $actorId): string
    {
        return "{$this->endpoint}/{$actorId}?api_key={$this->apiKey}";
    }

    /**
     * Wrap and return the collected data
     * @param array $data
     * @param int $status
     * @return array
     */
    private function returnData(array $data, int $status): array
    {
        $data['error'] = false;
        if ($status != 200) {
            $data['error'] = true;
        }

        $data['status'] = $status;

        return $data;
    }
}
