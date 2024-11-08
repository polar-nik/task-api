<?php namespace App\Exceptions;

use App\Facades\Answer;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomNotFoundHttpException extends NotFoundHttpException
{
    public function render($request): JsonResponse
    {
        return Answer::error(
            $this->getMessage(),
            null,
            Response::HTTP_NOT_FOUND
        );
    }
}
