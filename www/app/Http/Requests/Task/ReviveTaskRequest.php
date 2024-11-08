<?php namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property ?Task $task
 */
class ReviveTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = Task::withTrashed()->findOrFail($this->route('taskId'));

        return $this->user() && $this->user()->id === $task->user_id;
    }
}
