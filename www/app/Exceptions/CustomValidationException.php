<?php namespace App\Exceptions;

use App\Facades\Answer;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CustomValidationException extends ValidationException
{
    public function render($request): JsonResponse
    {
        return Answer::error(
            $this->getMessage(),
            $this->validator->errors()->getMessages(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
