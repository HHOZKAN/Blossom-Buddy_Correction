<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API Documentation",
 *      description="L5 Swagger OpenApi description", 
 * )
 */
class PlantController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/plants",
     *      operationId="getPlantsList",
     *      tags={"Plants"},
     *      summary="Get list of plants",
     *      description="Returns list of plants",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Plant")
     *          )
     *       )
     *     )
     */
    public function index(): JsonResponse
    {
        return response()->json(Plant::all());
    }


    /**
     * @OA\Post(
     *     path="/api/plants",
     *     operationId="storePlant",
     *     tags={"Plants"},
     *     summary="Store a new plant",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Plant")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plant created successfully"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $plant = Plant::create($request->all());

        return response()->json($plant, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/plants/{common_name}",
     *     operationId="getPlantByCommonName",
     *     tags={"Plants"},
     *     summary="Get plant by common name",
     *     description="Returns a single plant",
     *     @OA\Parameter(
     *         name="common_name",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="The common name of the plant"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Plant")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found"
     *     )
     * )
     */
    public function show(string $common_name): JsonResponse
    {
        $plant = Plant::where('common_name', 'LIKE', '%' . $common_name . '%')->firstOrFail();
        return response()->json($plant);
    }

    /**
     * @OA\Put(
     *     path="/api/plants/{common_name}",
     *     operationId="updatePlantByCommonName",
     *     tags={"Plants"},
     *     summary="Update an existing plant",
     *     description="Updates a plant by common_name",
     *     @OA\Parameter(
     *         name="common_name",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         description="common_name of the plant to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Plant")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Plant")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Content"
     *     )
     * )
     */
    public function update(Request $request, string $common_name): JsonResponse
    {

        // A FINIR ///////////////////////////////////////////////////////////////////
        $validatedData = $request->validate([
            'common_name' => 'sometimes|string|max:255',
            'watering_general_benchmark' => 'sometimes|array',
            'watering_general_benchmark.value' => 'sometimes|string',
            'watering_general_benchmark.unit' => 'sometimes|string',
        ]);

        try {
            $plant = Plant::where('common_name', 'LIKE', '%' . $common_name . '%')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Plant not found'], 404);
        }

        if (isset($validatedData['common_name'])) {
            $plant->common_name = $validatedData['common_name'];
        }
    
        if (isset($validatedData['watering_general_benchmark'])) {
            $wateringBenchmark = $plant->watering_general_benchmark;
    
            if (isset($validatedData['watering_general_benchmark']['value'])) {
                $wateringBenchmark['value'] = $validatedData['watering_general_benchmark']['value'];
            }
    
            if (isset($validatedData['watering_general_benchmark']['unit'])) {
                $wateringBenchmark['unit'] = $validatedData['watering_general_benchmark']['unit'];
            }
    
            $plant->watering_general_benchmark = $wateringBenchmark;
        }
    
        $plant->save();

        return response()->json($plant);
    }

     /**
     * @OA\Delete(
     *     path="/api/plants/{id}",
     *     operationId="deletePlant",
     *     tags={"Plants"},
     *     summary="Delete an existing plant",
     *     description="Delete a plant by id if it exists",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         description="id of the plant to delete"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Plant successfully deleted",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found"
     *     ),
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $plant = Plant::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Plant not found'], 404);
        }

        $plant->delete();

        return response()->json(null, 204);
    }
}
