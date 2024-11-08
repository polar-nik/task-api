<?php namespace App\Exceptions;

use App\Facades\Answer;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CustomAccessDeniedException extends AccessDeniedHttpException
{
    public function render($request): JsonResponse
    {
        return Answer::error(
            $this->getMessage(),
            null,
            Response::HTTP_FORBIDDEN
        );
    }
}
