<?php

namespace App\Http\Controllers;

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
    public function addPlantUser(Request $request): JsonResponse
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

        $user->plants()->attach($plant->id, ['city' => $city]);

        return response()->json(['message' => 'Plant added to user successfully'], 200);
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
