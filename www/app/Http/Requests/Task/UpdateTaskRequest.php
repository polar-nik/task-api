<?php namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user()) {
            $task = $this->route('task');

            return in_array($this->user()->id, [$task->user_id, $task->assigned_to_user_id]);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['string', 'min:3', 'max:255'],
            'description' => ['string', 'max:10000'],
            'assigned_to_user_id' => ['integer', 'exists:users,id'],
            'due_date' => ['date_format:H:i d/m/Y', 'after:now'],
        ];
    }
}
