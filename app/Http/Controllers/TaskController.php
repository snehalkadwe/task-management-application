<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Events\TaskCompleted;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->filled('status') || $request->filled('sort_by')) {
            // Skip cache and fetch directly if filters or sorting are applied
            $tasks = Task::where('user_id', auth()->id())
                ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
                ->orderBy($request->input('sort_by', 'due_date'), $request->input('sort_order', 'desc'))
                ->latest()
                ->paginate(5);
        } else {
            // Use cache only for default task listing
            $tasks = Cache::remember('tasks_' . auth()->id(), 60, function () {
                return Task::where('user_id', auth()->id())
                    ->orderBy('due_date', 'desc')
                    ->latest()
                    ->paginate(5);
            });
        }

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $params = $request->validated();
        Task::create([
            'title' => $params['title'],
            'description' => $params['description'],
            'due_date' => $params['due_date'],
            'status' => 'pending',
            'user_id' => Auth::user()->id,
        ]);
        $this->clearCache();
        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    /**
     * Display the task.
     */
    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        // The action is authorized...
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the task.
     */
    public function edit(Task $task)
    {
        Gate::authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update task.
     */
    public function update(Request $request, Task $task)
    {
        Gate::authorize('update', $task);
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date
        ]);
        $this->clearCache();
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    /**
     * Remove task from database.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();

        $this->clearCache();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }

    /**
     * Update task status to inprogrss or completed.
     *
     * @param Task $task
     * @return void
     */
    public function updateStatus(Task $task)
    {
        $task->status = $task->status === 'pending' ? 'in-progress' : 'completed';
        $task->save();

        // Fire event only when task is completed
        if ($task->status === 'completed') {
            event(new TaskCompleted($task));
        }
        $this->clearCache();
        return redirect()->route('tasks.index')->with('success', 'Task status updated successfully');
    }

    /**
     * Clear cache for tasks listing.
     */
    public function clearCache()
    {
        Cache::forget('tasks_' . auth()->id());
    }
}
