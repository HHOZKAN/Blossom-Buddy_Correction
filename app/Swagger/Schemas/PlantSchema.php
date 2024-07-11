<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Plant",
 *     type="object",
 *     title="Plant",
 *     properties={
 *         @OA\Property(
 *             property="common_name",
 *             type="string",
 *             description="The common name of the plant"
 *         ),
 *         @OA\Property(
 *             property="watering_general_benchmark",
 *             type="object",
 *             description="The general watering benchmark of the plant",
 *             @OA\Property(property="value", type="string"),
 *             @OA\Property(property="unit", type="string")
 *         )
 *     }
 * )
 */
class PlantSchema
{
}
