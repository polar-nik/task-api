<?php namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private ?User $user = null;
    private ?User $assignedUser = null;
    private ?Task $task = null;

    private array $taskData = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->assignedUser = User::factory()->create();
        $this->task = Task::factory()->create([
            'user_id' => $this->user->id,
            'assigned_to_user_id' => $this->assignedUser->id,
        ]);

        $this->taskData = [
            'title' => 'Title',
            'description' => 'Some description of the task',
            'assigned_to_user_id' => $this->assignedUser->id,
            'due_date' => '12:20 10/11/2025',
        ];
    }

    public function test_tasksList(): void
    {
        $response = $this->get(route('task.list'));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_createTask(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('task.store'), $this->taskData);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'task']);
    }

    public static function invalidCreateTaskProvider(): Generator
    {
        yield 'empty-field' => [
            [
                'title' => 'Title',
                'description' => 'Some description of the task',
                'due_date' => '12:20 10/11/2025',
            ],
            422
        ];

        yield 'wrong-date' => [
            [
                'title' => 'Title',
                'description' => 'Some description of the task',
                'due_date' => '12:20:20 10/11/2025',
            ],
            422
        ];
    }

    #[DataProvider('invalidCreateTaskProvider')]
    public function test_failedCreateTask(array $data, int $assertedStatus)
    {
        $response = $this->actingAs($this->user)->postJson(route('task.store'), $data);

        $response->assertStatus($assertedStatus);
    }

    public function test_failedUnauthorizedCreateTask()
    {
        $response = $this->postJson(route('task.store'), $this->taskData);

        $response->assertStatus(401);
    }

    public function test_showTask(): void
    {
        $response = $this->get(route('task.show', $this->task->id));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'id']);
    }

    public function test_notFoundTask(): void
    {
        $response = $this->get(route('task.show', 10000));

        $response->assertStatus(404);
    }

    public function test_updateTask(): void
    {
        $response = $this->actingAs($this->user)->patchJson(route('task.update', $this->task->id), [
            'description' => 'Another description of the task',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'id']);
    }

    public function test_unauthorizedUpdateTask(): void
    {
        $response = $this->patchJson(route('task.update', $this->task->id), [
            'title' => 'Another title',
        ]);

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    public function test_updateNotFoundTask(): void
    {
        $response = $this->actingAs($this->user)->patchJson(route('task.update', 10000), [
            'due_date' => '12:20 10/11/2026',
        ]);

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_wrongFormatUpdateTask(): void
    {
        $response = $this->actingAs($this->user)->patchJson(route('task.update', $this->task->id), [
            'due_date' => '12:20:20 10/11/2026',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_updateTaskStatus(): void
    {
        $response = $this->actingAs($this->user)->patchJson(route('task.update-status', $this->task->id), [
            'status_id' => TaskStatus::InProgress->value,
        ]);

        $response->assertStatus(200)
            ->assertExactJson(['success' => true]);
    }

    public function test_wrongFormatUpdateTaskStatus(): void
    {
        $response = $this->actingAs($this->user)->patchJson(route('task.update-status', $this->task->id), [
            'status_id' => 10000,
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_deleteTask(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('task.destroy', $this->task->id));

        $response->assertStatus(200)
            ->assertExactJson(['success' => true]);
    }

    public function test_deleteNotFoundTask(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('task.destroy', 10000));

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_reviveTask(): void
    {
        $this->actingAs($this->user)->deleteJson(route('task.destroy', $this->task->id));

        $response = $this->actingAs($this->user)->postJson(route('task.revive', ['taskId' => $this->task->id]));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'id']);
    }

    public function test_reviveNotFoundTask(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('task.revive', 10000));

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }
}
