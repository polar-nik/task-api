<?php namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property integer $status_id
 * @property string $due_date
 */
class ListTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status_id' => ['integer', Rule::enum(TaskStatus::class)],
            'due_date' => ['date_format:H:i d/m/Y'],
            'per_page' => ['integer', 'min:1', 'max:10000'],
        ];
    }
}
