<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     properties={
 *         @OA\Property(
 *             property="firstName",
 *             type="string",
 *             example="John",
 *             description="The first name of the user"
 *         ),
 *         @OA\Property(
 *             property="lastName",
 *             type="string",
 *             example="Doe",
 *             description="The last name of the user"
 *         ),
 *         @OA\Property(
 *             property="email",
 *             type="string",
 *             example="john.doe@example.com",
 *             description="The email of the user"
 *         ),
 *     }
 * )
 */
class UserSchema
{
}
