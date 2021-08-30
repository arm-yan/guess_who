<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovieService\ActorsService;
use GuzzleHttp\Exception\GuzzleException;
use function Symfony\Component\Translation\t;

class QuizController
{
    /**
     * @var ActorsService
     */
    private ActorsService $actorsService;

    /**
     * @param ActorsService $actorsService
     */
    public function __construct(ActorsService $actorsService)
    {
        $this->actorsService = $actorsService;
    }

    /**
     * Get quiz question and options
     * @throws GuzzleException
     */
    public function getQuiz()
    {
        $actors = $this->getPopularActors();
        $image = $this->getRandomFromDeck($actors, 'imagePath');

        $quizData = [
            'question'  => $this->actorsService->getImageFullPath($image),
            'options' => $this->getOptions($actors)
        ];

        $this->showResponse($quizData, 200);
    }

    /**
     * Resolve the given answer
     * @throws GuzzleException
     */
    public function answer(Request $request)
    {
        $answer_id = $request->has('answer_id') ? $request->get('answer_id') : null;
        $question = $request->has('question') ? $request->get('question') : null;
        $details = $this->actorsService->getActorDetails($answer_id);

        if($details['error']) {
            $this->showResponse($details, $details['status']);
        }

        $profileData = json_decode($details['data'], true);
        $result = $this->compareTheImagePath($profileData['profile_path'], $question);


        $this->showResponse(['correct' => $result], 200);
    }

    /**
     * Compare the given answers image path with quiz image path
     * @param string|null $answerImagePath
     * @param string|null $quizImagePath
     * @return bool
     */
    private function compareTheImagePath(?string $answerImagePath, ?string $quizImagePath): bool
    {
        $quizPathParts = explode('/', $quizImagePath);

        if($answerImagePath == '/'.end($quizPathParts)) {
            return true;
        }

        return false;
    }

    /**
     * Get popular actors collection
     * @return array
     * @throws GuzzleException
     */
    private function getPopularActors(): array
    {
        $data = $this->actorsService->getPopular();

        if($data['error']) {
            $this->showResponse($data, $data['status']);
        }

        return $this->actorsService->parseActorsData($data['data'], 5);
    }

    /**
     * Get quiz options
     * @param array $actors
     * @return array
     */
    private function getOptions(array $actors): array
    {
        return $this->actorsService->parseActorsNames($actors);
    }

    /**
     * Pick random one value from given collection by given key
     * @param array $deck
     * @param string $key
     * @return string
     */
    private function getRandomFromDeck(array $deck, string $key): string
    {
        return $deck[array_rand($deck)][$key];
    }

    /**
     * Wrap and send the collected data as a json
     * @param array $data
     * @param int $status
     */
    private function showResponse(array $data, int $status)
    {
        response()->json($data, $status)->send();
    }
}
