<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Plant::all());
    }

    public function store(Request $request): JsonResponse
    {
        $plant = Plant::create($request->all());

        return response()->json($plant, 201);
    }

    public function show(string $common_name): JsonResponse
    {
        $plant = Plant::where('common_name', 'LIKE', '%' . $common_name . '%')->firstOrFail();
        return response()->json($plant);
    }

    public function update(Request $request, string $common_name): JsonResponse
    {
        $plant = Plant::where('common_name', 'LIKE', '%' . $common_name . '%')->firstOrFail();
        $plant->update($request->all());

        return response()->json($plant);
    }

    public function destroy(string $common_name): JsonResponse
    {
        $plant = Plant::where('common_name', 'LIKE', '%' . $common_name . '%')->firstOrFail();
        $plant->delete();

        return response()->json(null, 204);
    }
}
