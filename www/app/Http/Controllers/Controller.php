<?php namespace App\Http\Controllers;

/**
 * @OA\Info(title="API documentation", version="0.1")
 * @OA\SecurityScheme(
 *       securityScheme="BearerAuth",
 *       in="header",
 *       name="BearerAuth",
 *       type="http",
 *       scheme="bearer",
 *       bearerFormat="JWT",
 *  ),
 * @OA\Tag(
 *      name="Auth",
 *      description="Auth endpoints",
 *  )
 * @OA\Tag(
 *      name="Users",
 *      description="Users endpoints",
 *  )
 */
abstract class Controller
{
    //
}
