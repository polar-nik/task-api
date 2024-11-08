<?php namespace App\Http\Controllers;

use App\Facades\Answer;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Requests\Auth\SignOutRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public const string AUTH_TOKEN_NAME = 'auth';

    /**
     * @OA\Post(
     *     path="/auth/sign-up",
     *     tags={"Auth"},
     *     summary="Sign up",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="Ivan"),
     *             @OA\Property(property="email", type="string", format="email", example="user1@email.com"),
     *             @OA\Property(property="password", type="string", format="password", example="notless6"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="token", type="string", example="2|ua3LsYWaAfLqBCIZ3ULdwUGhzqA3PA25egKEJQ0N6c261c8e")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Content",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="The email field must be a valid email address."),
     *              @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                    property="email",
     *                    type="array",
     *                    collectionFormat="multi",
     *                    @OA\Items(
     *                       type="string",
     *                       example="The email field is required.",
     *                    )
     *                 )
     *              )
     *          )
     *     )
     * )
     */
    public function signUp(SignUpRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return Answer::success(['token' => $user->createPlainTextToken(self::AUTH_TOKEN_NAME)]);
    }

    /**
     * @OA\Post(
     *     path="/auth/sign-in",
     *     tags={"Auth"},
     *     summary="Sign in",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user1@email.com"),
     *             @OA\Property(property="password", type="string", format="password", example="notless6"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="token", type="string", example="2|ua3LsYWaAfLqBCIZ3ULdwUGhzqA3PA25egKEJQ0N6c261c8e")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Content",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="The email field must be a valid email address."),
     *              @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                    property="email",
     *                    type="array",
     *                    collectionFormat="multi",
     *                    @OA\Items(
     *                       type="string",
     *                       example="The email field is required.",
     *                    )
     *                 )
     *              )
     *          )
     *     )
     * )
     */
    public function signIn(SignInRequest $request): JsonResponse
    {
        $obtainUser = User::where('email', $request->email)->first();

        if (Hash::check($request->password, $obtainUser->password)) {

            return Answer::success(['token' => $obtainUser->createPlainTextToken(self::AUTH_TOKEN_NAME)]);
        }

        return Answer::error(
            'Wrong password',
            ['password' => ['Email and password did not match']],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @OA\Post(
     *     path="/auth/sign-out",
     *     tags={"Auth"},
     *     summary="Sign out",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - when token not persists",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="This action is unauthorized."),
     *          )
     *     )
     * )
     */
    public function signOut(SignOutRequest $request):JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return Answer::success();
    }

    public function login()
    {
        return Answer::error(
            'Unauthorized',
            ['auth' => ['You must be logged in, before doing this']],
            Response::HTTP_FORBIDDEN
        );
    }
}
