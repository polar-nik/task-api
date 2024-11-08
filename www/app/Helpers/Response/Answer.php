<?php namespace App\Helpers\Response;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Answer
{
    /**
     * Response to a successful action.
     *
     * @param array|string|null $additional_data
     * @param int                   $code
     *
     * @return JsonResponse
     */
    public function success(array|string $additional_data = null, int $code = Response::HTTP_OK): JsonResponse
    {
        $answer = ['success' => true];

        if (!empty($additional_data)) {
            if (is_array($additional_data)) {
                $answer = array_merge($answer, $additional_data);
            } else if (is_string($additional_data)) {
                $answer['message'] = $additional_data;
            }
        }

        return $this->raw($answer, $code);
    }

    /**
     * Response to a successful action.
     *
     * @param string     $message
     * @param array|null $errors
     * @param int        $code
     *
     * @return JsonResponse
     */
    public function error(string $message, ?array $errors = null, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $answer = [
            'success' => false,
            'message' => $message
        ];

        if (!empty($errors)) {
            $answer['errors'] = $errors;
        }

        return $this->raw($answer, $code);
    }

    /**
     * @param array $data
     * @param int $code
     *
     * @return JsonResponse
     */
    public function raw(array $data, int $code): JsonResponse
    {
        return response()->json(
            $data,
            $code,
            [
                'Content-Type' => 'application/json;charset=UTF-8',
                'Charset' => 'utf-8'
            ],
            JSON_UNESCAPED_UNICODE
        );
    }
}
