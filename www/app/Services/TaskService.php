<?php namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    public const int TASKS_PER_PAGE = 30;

    public function filteredTasks(array $filters): LengthAwarePaginator
    {
        return Task::when(isset($filters['due_date']), fn ($query) =>
            $query->where('due_date', '<', Carbon::parse($filters['due_date']))
        )->when(isset($filters['status_id']), fn ($query) =>
            $query->where('status_id', $filters['status_id'])
        )->paginate(
            $filters['per_page'] ?? self::TASKS_PER_PAGE
        )->appends($filters);
    }

    public function foundTasks(array $filters): LengthAwarePaginator
    {
        return Task::where('title', 'like', '%' . $filters['q'] .'%')
            ->orWhere('description', 'like', '%' . $filters['q'] .'%')
            ->paginate($filters['per_page'] ?? self::TASKS_PER_PAGE)
            ->appends($filters);
    }
}
