<?php namespace App\Facades;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static JsonResponse success(array|string $additional_data = null, int $code = 200)
 * @method static JsonResponse error(string $message, null|array $errors = null, int $code = 400)
 * @method static JsonResponse raw(array $data, int $code)
 */
class Answer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'answer';
    }
}
