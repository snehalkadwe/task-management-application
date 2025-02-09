<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_tasks()
    {
        $this->actingAs($this->user);
        Task::factory()->create();

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tasks');
    }

    public function test_user_can_create_a_task()
    {
        $this->actingAs($this->user);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Task description',
            'due_date' => now()->addDay()->format('Y-m-d'),
            'user_id' => $this->user->id,
        ];

        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_user_task_created_with_pending_status()
    {
        $this->actingAs($this->user);

        $taskData = [
            'title' => 'Pending Task',
            'description' => 'Task with pending status',
            'due_date' => now()->addDays(2),
            'status' => 'pending',
        ];

        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['title' => 'Pending Task', 'status' => 'pending']);
    }

    public function test_user_can_create_another_task()
    {
        $this->actingAs($this->user);

        $taskData = [
            'title' => 'Another Task',
            'description' => 'Another task description',
            'due_date' => now()->addWeek()->format('Y-m-d'),
        ];

        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_user_can_view_a_single_task()
    {
        $this->actingAs($this->user);
        $task = Task::factory()->create();

        $response = $this->get(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertViewHas('task');
    }

    public function test_user_can_update_a_task()
    {
        $this->actingAs($this->user);
        $task = Task::factory()->create();

        $updatedData = [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'due_date' => now()->addDays(2)->format('Y-m-d'),
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', $updatedData);
    }

    public function test_user_can_delete_a_task()
    {
        $this->actingAs($this->user);
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
