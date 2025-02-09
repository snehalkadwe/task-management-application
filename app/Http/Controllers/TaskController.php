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
        // Generate unique cache key based on user and request parameters
        $cacheKey = 'tasks_' . auth()->user()->id . '_' . md5(json_encode($request->all()));

        // Retrieve tasks from cache or query the database if cache is missing
        // $tasks = Cache::remember($cacheKey, 300, function () use ($request) {
        $tasks = Task::with('user')
            ->where('user_id', auth()->user()->id)
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->orderBy($request->input('sort_by', 'due_date'), $request->input('sort_order', 'desc'))
            ->paginate(5);
        // });

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
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    /**
     * Remove task from database.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();
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

        return redirect()->route('tasks.index')->with('success', 'Task status updated successfully');
    }
}
