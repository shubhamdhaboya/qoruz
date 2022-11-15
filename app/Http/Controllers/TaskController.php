<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Action to add task
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string',
            'due_date' => 'required|date',
            'parent_id' => 'nullable|exists:tasks,id'
        ]);

        $task = Task::create($data);

        return response()->json(['task' => $task]);
    }

    /**
     * Action to delete task
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function delete(Task $task): JsonResponse
    {
        return response()->json(['success' => $task->delete()]);
    }

    /**
     * Action to update task
     *
     * @param Task $task
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Task $task, Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'string',
            'due_date' => 'date',
            'status' => 'required|in:'.implode(",", Task::STATUS)
        ]);
        $task->update($data);

        // update children if status is changed
        if(array_key_exists('status', $data)) {
            $children = $task
                ->children()
                ->withTrashed() // to updated deleted task as well
                ->where('status', '<>', $data['status']) // to avoid fetching task that have already updated status
                ->get()
            ;

            foreach($children as $child) {
                $child->status = $data['status'];
                $child->save();
            }
        }

        return response()->json(['task' => $task]);
    }

    public function get(Request $request)
    {
        $data = $request->only(['title', 'due_on']);
        $tasks = Task::where([
            ['status', '=', Task::STATUS_PENDING],
        ]);

        if(array_key_exists('title', $data)) {
            $title = "%" . $data['title'] . "%";
            $tasks->where('title', 'LIKE', $title);
        }

        if(array_key_exists('due_on', $data)) {
            switch ($data['due_on']) {
                case 'today':
                    $tasks->where('due_date', '=', Carbon::today());
                    break;
                    case 'this_week':
                        $tasks->whereBetween('due_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                    case 'next_week':
                        $tasks->whereBetween('due_date', [Carbon::now()->endOfWeek()->addDay(), Carbon::now()->endOfWeek()->addDays(7)]);
                    break;
                    case 'overdue':
                        $tasks->where('due_date', '<', Carbon::today());
                    break;
            }
        }

        return response()->json($tasks->paginate());
    }
}
