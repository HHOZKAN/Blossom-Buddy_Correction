<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;

class UserPlantController extends Controller
{

    // A TESTER
    public function addPlantUser(Request $request) // ajouter la valeur de retour
    {
        $validated = $request->validate([
            'plant_name' => 'required|string',
            'city' => 'required|string',
        ]);

        $user = $request->user();
        $plant = Plant::where('common_name', 'LIKE', '%' . $validated['plant_name'] . '%')->firstOrFail();
        if (!$plant) {
            return response()->json(['error' => 'Plant not found'], 404);
        }
        $city = $validated['city'];

        $user->plants()->attach($plant->id, ['city' => $city]);

        // Message bon ?
        return response()->json(['message' => 'Plant added to user successfully'], 200);
    }

    public function deletePlantUser()
    {
        //
    }
}
