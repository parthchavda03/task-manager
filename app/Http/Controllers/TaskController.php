<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort by sort_order ascending, then by created_at descending
        $tasks = $query->orderBy('sort_order', 'asc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();

        // Get max sort_order to place the new task at the bottom
        $maxOrder = Task::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $validated = $request->validated();

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    /**
     * Toggle status of the specified task.
     */
    public function toggleStatus(Request $request, Task $task)
    {
        $task->status = $task->status === TaskStatus::COMPLETED ? TaskStatus::PENDING : TaskStatus::COMPLETED;
        $task->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $task->status->value,
                'message' => 'Task status updated successfully.'
            ]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task status updated.');
    }

    /**
     * Update order of tasks from drag-and-drop.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:tasks,id',
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $id) {
            Task::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task order updated successfully.'
        ]);
    }
}
