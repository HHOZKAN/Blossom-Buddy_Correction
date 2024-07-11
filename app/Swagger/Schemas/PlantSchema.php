<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Plant",
 *     type="object",
 *     title="Plant",
 *     @OA\Property(
 *         property="common_name",
 *         type="string",
 *         example="Aloe Vera",
 *         description="The common name of the plant"
 *     ),
 *     @OA\Property(
 *         property="watering_general_benchmark",
 *         type="object",
 *         description="The general watering benchmark of the plant",
 *         @OA\Property(property="value", type="string", example="1"),
 *         @OA\Property(property="unit", type="string", example="week")
 *     )
 * )
 * 
 * @OA\Examples(
 *     summary="Example of a Plant",
 *     example="plant",
 *     value={
 *         "common_name": "Aloe Vera",
 *         "watering_general_benchmark": {
 *             "value": "1",
 *             "unit": "week"
 *         }
 *     }
 * )
 */
class PlantSchema
{
}
