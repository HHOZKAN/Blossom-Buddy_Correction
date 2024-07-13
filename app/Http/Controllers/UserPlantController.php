<?php

namespace App\Http\Controllers;

use App\Interfaces\WeatherServiceInterface;
use App\Jobs\SendWateringReminder;
use App\Models\Plant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPlantController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/user/plants",
     *      operationId="getPlantsUser",
     *      tags={"UserPlants"},
     *      summary="Get user's plants",
     *      description="Returns the list of plants that the authenticated user possesses",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Plant")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function getPlantsUser(Request $request): JsonResponse
    {
        /**
         * @var \App\Models\User $user
         */
        $user = $request->user();

        $plants = $user->plants;

        return response()->json($plants, 200);
    }

    /**
     * @OA\Post(
     *      path="/api/user/plant",
     *      operationId="addPlantUser",
     *      tags={"UserPlants"},
     *      summary="Add a plant to user's list",
     *      description="Allows an authenticated user to add a plant to their list",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"plant_name", "city"},
     *              @OA\Property(property="plant_name", type="string", example="Rose"),
     *              @OA\Property(property="city", type="string", example="Paris")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Plant added to user successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Plant added to user successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Plant not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Plant not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function addPlantUser(Request $request, WeatherServiceInterface $weatherService): JsonResponse
    {

        $validated = $request->validate([
            'plant_name' => 'required|string',
            'city' => 'required|string',
        ]);

        /**
         * @var \App\Models\User $user
         */
        $user = $request->user();

        $plant = Plant::where('common_name', 'LIKE', '%' . $validated['plant_name'] . '%')->firstOrFail();
        if (!$plant) {
            return response()->json(['error' => 'Plant not found'], 404);
        }

        $city = $validated['city'];

        // Extract watering benchmarks from the plant
        $wateringBenchmark = $plant->watering_general_benchmark;
        $unit = $wateringBenchmark['unit'];
        $value = $wateringBenchmark['value'];

        // Calculate the number of days until the next watering
        $daysUntilNextWatering = 0;
        if ($unit === 'days') {
            $range = explode('-', $value);
            $daysUntilNextWatering = (int) $range[0]; // Taking the lower bound of the range
        } elseif ($unit === 'week') {
            $range = explode('-', $value);
            $daysUntilNextWatering = (int) $range[0] * 7; // Convert weeks to days
        }

        // Determine the number of days to pass to the weather service
        $daysForWeatherService = $daysUntilNextWatering >= 5 ? 5 : $daysUntilNextWatering;

        // Use the weather service to get the forecast for the city
        $weatherData = $weatherService->getWeatherForecast($city, $daysForWeatherService);

        // We will convert days into hours to be able to delay the job
        $hoursUntilNextWatering = $daysUntilNextWatering * 24;

        // For each day in the forecast, calculate a coefficient and apply it to the days until next watering
        foreach ($weatherData as $day) {
            $humidity = $day['avghumidity'];

            // For eache 10% above 70%, we add 10% to daysUntilNextWatering
            if ($humidity > 70) {
                $tranchesAbove70 = floor(($humidity - 70) / 10) + 1;
                $hoursUntilNextWatering += $hoursUntilNextWatering * (0.1 * $tranchesAbove70);
            }
            // For each 10% under 40%, we remove 10% to daysUntilNextWatering
            elseif ($humidity < 40) {
                $tranchesBelow40 = floor((40 - $humidity) / 10) - 1;
                $hoursUntilNextWatering -= $hoursUntilNextWatering * (0.1 * $tranchesBelow40);
            }
        }

        // Convert hours into days + hours for the delay
        $days = floor($hoursUntilNextWatering / 24);
        $hours = $hoursUntilNextWatering % 24;

        $user->plants()->attach($plant->id, ['city' => $city]);

        // Dispatch the Laravel job to send the reminder
        SendWateringReminder::dispatch($user, $plant->common_name)->delay(now()->addDays($days)->addHours($hours));


        return response()->json([
            'message' => 'Plant added to user successfully',
            'daysUntilNextWatering' => $days,
            'hoursUntilNextWatering' => $hours
        ], 200);
    }


    /**
     * @OA\Delete(
     *      path="/api/user/plant/{id}",
     *      operationId="deletePlantUser",
     *      tags={"UserPlants"},
     *      summary="Delete a plant from user's list",
     *      description="Allows an authenticated user to delete a plant from their list",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Plant ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Plant deleted from user successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Plant deleted from user successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Plant not found in user",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Plant not found in user")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     * )
     */
    public function deletePlantUser(Request $request, int $id): JsonResponse
    {
        /**
         * @var \App\Models\User $user
         */
        $user = $request->user();

        $relation = $user->plants()->wherePivot('id', $id)->first();

        if (!$relation) {
            return response()->json(['error' => 'Plant not found in user'], 404);
        }

        $user->plants()->wherePivot('id', $id)->detach();

        return response()->json(['message' => 'Plant deleted from user successfully'], 200);
    }
}
