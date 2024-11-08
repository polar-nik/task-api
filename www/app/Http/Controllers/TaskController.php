<?php namespace App\Http\Controllers;

use App\Facades\Answer;
use App\Http\Requests\Task\DeleteTaskRequest;
use App\Http\Requests\Task\ListTaskRequest;
use App\Http\Requests\Task\ReviveTaskRequest;
use App\Http\Requests\Task\SearchRequest;
use app\Http\Requests\Task\StoreTaskRequest;
use app\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $service
    ) {}

    /**
     * @OA\Get(
     *     path="/api/task",
     *     tags={"Task"},
     *     summary="Tasks list",
     *     @OA\Parameter(name="status_id", in="query", example="1"),
     *     @OA\Parameter(name="due_date", in="query", example="20:20 10/10/2025"),
     *     @OA\Parameter(name="page", in="query", example="1"),
     *     @OA\Parameter(name="per_page", in="query", example="30"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="current_page", type="integer", example="1"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="title", type="string", example="Task title"),
     *                      @OA\Property(property="description", type="string", example="Some task description"),
     *                      @OA\Property(property="status_id", type="integer", example="1"),
     *                      @OA\Property(property="user_id", type="integer", example="1"),
     *                      @OA\Property(property="assigned_to_user_id", type="integer", example="1"),
     *                      @OA\Property(property="due_date", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *                      @OA\Property(property="created_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *                      @OA\Property(property="updated_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *                      @OA\Property(property="deleted_at", type="string", format="datetime", example="null"),
     *                  ),
     *              ),
     *              @OA\Property(property="first_page_url", type="string", example="http://localhost:8080/api/task?page=1"),
     *              @OA\Property(property="from", type="integer", example="1"),
     *              @OA\Property(property="last_page", type="integer", example="2"),
     *              @OA\Property(property="last_page_url", type="string", example="http://localhost:8080/api/task?page=2"),
     *              @OA\Property(property="links", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="url", type="string", example="http://localhost:8080/api/task?page=2"),
     *                      @OA\Property(property="label", type="string", example="2"),
     *                      @OA\Property(property="active", type="boolean", example="false"),
     *                  ),
     *              ),
     *              @OA\Property(property="next_page_url", type="string", example="http://localhost:8080/api/task?page=2"),
     *              @OA\Property(property="path", type="string", example="http://localhost:8080/api/task"),
     *              @OA\Property(property="per_page", type="integer", example="30"),
     *              @OA\Property(property="prev_page_url", type="string", example="null"),
     *              @OA\Property(property="to", type="integer", example="2"),
     *              @OA\Property(property="total", type="integer", example="2"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Content",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="The due date field must match the format H:i d/m/Y."),
     *              @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                    property="due_date",
     *                    type="array",
     *                    collectionFormat="multi",
     *                    @OA\Items(
     *                       type="string",
     *                       example="The due date field must match the format H:i d/m/Y.",
     *                    )
     *                 )
     *              )
     *          )
     *     )
     * )
     */
    public function index(ListTaskRequest $request): JsonResponse
    {
        return Answer::success(
            $this->service->filteredTasks($request->validated())->toArray()
        );
    }

    /**
     * @OA\Post(
     *     path="/api/task/store",
     *     tags={"Task"},
     *     summary="Create task",
     *      security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task data",
     *         @OA\JsonContent(
     *             required={"title", "assigned_to_user_id", "due_date"},
     *             @OA\Property(property="title", type="string", example="Task title"),
     *             @OA\Property(property="description", type="string", example="Some task description"),
     *             @OA\Property(property="assigned_to_user_id", type="integer", example="1"),
     *             @OA\Property(property="due_date", type="string", format="datetime", example="20:20 10/10/2025"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(
     *                  property="task",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="title", type="string", example="Task title"),
     *                  @OA\Property(property="description", type="string", example="Some task description"),
     *                  @OA\Property(property="status_id", type="integer", example="1"),
     *                  @OA\Property(property="user_id", type="integer", example="1"),
     *                  @OA\Property(property="assigned_to_user_id", type="integer", example="1"),
     *                  @OA\Property(property="due_date", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *                  @OA\Property(property="created_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *                  @OA\Property(property="deleted_at", type="string", format="datetime", example="null"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="message", type="string", example="Unauthenticated."),
     *               @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(property="auth", type="string",  example="Unauthenticated"),
     *               )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Content",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="The assigned to user id field is required."),
     *              @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                    property="assigned_to_user_id",
     *                    type="array",
     *                    collectionFormat="multi",
     *                    @OA\Items(
     *                       type="string",
     *                       example="The assigned to user id field is required",
     *                    )
     *                 )
     *              )
     *          )
     *     )
     * )
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        return Answer::success([
            'task' => $request->user()->createdTasks()->create($request->validated())->fresh(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/task/{id}",
     *     tags={"Task"},
     *     summary="Task data",
     *     @OA\Parameter(name="id", in="path", example="1"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="title", type="string", example="Task title"),
     *              @OA\Property(property="description", type="string", example="Some task description"),
     *              @OA\Property(property="status_id", type="integer", example="1"),
     *              @OA\Property(property="user_id", type="integer", example="1"),
     *              @OA\Property(property="assigned_to_user_id", type="integer", example="1"),
     *              @OA\Property(property="due_date", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *              @OA\Property(property="created_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *              @OA\Property(property="updated_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *              @OA\Property(property="deleted_at", type="string", format="datetime", example="null"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Task] 13"),
     *          )
     *     )
     * )
     */
    public function show(Task $task)
    {
        return Answer::success($task->toArray());
    }

    /**
     * @OA\Patch(
     *     path="/api/task/{id}",
     *     tags={"Task"},
     *     summary="Update task data",
     *     @OA\Parameter(name="id", in="path", example="1"),
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task data",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Task title"),
     *             @OA\Property(property="description", type="string", example="Some task description"),
     *             @OA\Property(property="assigned_to_user_id", type="integer", example="1"),
     *             @OA\Property(property="due_date", type="string", format="datetime", example="20:20 10/10/2025"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="title", type="string", example="Task title"),
     *              @OA\Property(property="description", type="string", example="Some task description"),
     *              @OA\Property(property="status_id", type="integer", example="1"),
     *              @OA\Property(property="user_id", type="integer", example="1"),
     *              @OA\Property(property="assigned_to_user_id", type="integer", example="1"),
     *              @OA\Property(property="due_date", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *              @OA\Property(property="created_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *              @OA\Property(property="updated_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *              @OA\Property(property="deleted_at", type="string", format="datetime", example="null"),
     *         )
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *                @OA\Property(property="success", type="boolean", example="false"),
     *                @OA\Property(property="message", type="string", example="This action is unauthorized."),
     *          )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Task] 13"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Content",
     *          @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="message", type="string", example="The selected status id is invalid"),
     *               @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                     property="status_id",
     *                     type="array",
     *                     collectionFormat="multi",
     *                     @OA\Items(
     *                        type="string",
     *                        example="The selected status id is invalid",
     *                     )
     *                  )
     *               )
     *           )
     *      )
     * )
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return Answer::success($task->fresh()->toArray());
    }

    /**
     * @OA\Patch(
     *     path="/api/task/{id}/status",
     *     tags={"Task"},
     *     summary="Update task status",
     *     @OA\Parameter(name="id", in="path", example="1"),
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task data",
     *         @OA\JsonContent(
     *             required={"title", "assigned_to_user_id", "due_date"},
     *             @OA\Property(property="status_id", type="integer", example="1"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *         )
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *                @OA\Property(property="success", type="boolean", example="false"),
     *                @OA\Property(property="message", type="string", example="This action is unauthorized."),
     *          )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Task] 13"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Content",
     *          @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="message", type="string", example="The selected status id is invalid"),
     *               @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                     property="status_id",
     *                     type="array",
     *                     collectionFormat="multi",
     *                     @OA\Items(
     *                        type="string",
     *                        example="The selected status id is invalid",
     *                     )
     *                  )
     *               )
     *           )
     *      )
     * )
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        $task->update(['status_id' => $request->status_id]);

        return Answer::success();
    }

    /**
     * @OA\Delete(
     *     path="/api/task/{id}",
     *     tags={"Task"},
     *     summary="Put Task in trash",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", example="1"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *         )
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *                @OA\Property(property="success", type="boolean", example="false"),
     *                @OA\Property(property="message", type="string", example="This action is unauthorized."),
     *          )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Task] 13"),
     *          )
     *     ),
     * )
     */
    public function destroy(DeleteTaskRequest $request, Task $task): JsonResponse
    {
        $task->delete();

        return Answer::success();
    }

    /**
     * @OA\Get(
     *     path="/api/task/search",
     *     tags={"Task"},
     *     summary="Search in title and discription",
     *     @OA\Parameter(name="q", in="query", example="search"),
     *     @OA\Parameter(name="page", in="query", example="1"),
     *     @OA\Parameter(name="per_page", in="query", example="30"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="current_page", type="integer", example="1"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="title", type="string", example="Task title"),
     *                      @OA\Property(property="description", type="string", example="Some task description"),
     *                      @OA\Property(property="status_id", type="integer", example="1"),
     *                      @OA\Property(property="user_id", type="integer", example="1"),
     *                      @OA\Property(property="assigned_to_user_id", type="integer", example="1"),
     *                      @OA\Property(property="due_date", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *                      @OA\Property(property="created_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *                      @OA\Property(property="updated_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *                      @OA\Property(property="deleted_at", type="string", format="datetime", example="null"),
     *                  ),
     *              ),
     *              @OA\Property(property="first_page_url", type="string", example="http://localhost:8080/api/task?page=1"),
     *              @OA\Property(property="from", type="integer", example="1"),
     *              @OA\Property(property="last_page", type="integer", example="2"),
     *              @OA\Property(property="last_page_url", type="string", example="http://localhost:8080/api/task?page=2"),
     *              @OA\Property(property="links", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="url", type="string", example="http://localhost:8080/api/task?page=2"),
     *                      @OA\Property(property="label", type="string", example="2"),
     *                      @OA\Property(property="active", type="boolean", example="false"),
     *                  ),
     *              ),
     *              @OA\Property(property="next_page_url", type="string", example="http://localhost:8080/api/task?page=2"),
     *              @OA\Property(property="path", type="string", example="http://localhost:8080/api/task"),
     *              @OA\Property(property="per_page", type="integer", example="30"),
     *              @OA\Property(property="prev_page_url", type="string", example="null"),
     *              @OA\Property(property="to", type="integer", example="2"),
     *              @OA\Property(property="total", type="integer", example="2"),
     *         )
     *     ),
     * )
     */
    public function search(SearchRequest $request): JsonResponse
    {
        return Answer::success(
            $this->service->foundTasks($request->validated())->toArray()
        );
    }

    /**
     * @OA\Post(
     *     path="/api/task/{id}/revive",
     *     tags={"Task"},
     *     summary="Returns Task from trash",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", example="1"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="title", type="string", example="Task title"),
     *              @OA\Property(property="description", type="string", example="Some task description"),
     *              @OA\Property(property="status_id", type="integer", example="1"),
     *              @OA\Property(property="user_id", type="integer", example="1"),
     *              @OA\Property(property="assigned_to_user_id", type="integer", example="1"),
     *              @OA\Property(property="due_date", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *              @OA\Property(property="created_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *              @OA\Property(property="updated_at", type="string", format="datetime", example="2024-10-10T20:20:00.000000Z"),
     *              @OA\Property(property="deleted_at", type="string", format="datetime", example="null"),
     *         )
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *                @OA\Property(property="success", type="boolean", example="false"),
     *                @OA\Property(property="message", type="string", example="This action is unauthorized."),
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Task] 13"),
     *          )
     *     )
     * )
     */
    public function revive(ReviveTaskRequest $request, int $taskId): JsonResponse
    {
        $task = Task::withTrashed()->findOrFail($taskId);

        if ($task->trashed()) {
            $task->restore();

            return Answer::success($task->fresh()->toArray());
        }

        throw (new ModelNotFoundException())->setModel(Task::class);
    }
}
