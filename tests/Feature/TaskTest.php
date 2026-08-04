<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test home page redirects to tasks index.
     */
    public function test_home_redirects_to_tasks_index(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('tasks.index'));
    }

    /**
     * Test tasks index page loads successfully.
     */
    public function test_tasks_index_loads_successfully(): void
    {
        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200);
        $response->assertSee('TaskSpace');
    }

    /**
     * Test task creation validation and storage.
     */
    public function test_can_create_task(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Test Task Title',
            'description' => 'Test Task Description',
            'status' => TaskStatus::PENDING->value,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task Title',
            'description' => 'Test Task Description',
            'status' => TaskStatus::PENDING->value,
            'sort_order' => 1
        ]);
    }

    /**
     * Test task creation fails validation with missing title.
     */
    public function test_cannot_create_task_without_title(): void
    {
        $response = $this->post(route('tasks.store'), [
            'description' => 'Test Task Description',
            'status' => TaskStatus::PENDING->value,
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    /**
     * Test task creation fails validation with invalid status.
     */
    public function test_cannot_create_task_with_invalid_status(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Test Task',
            'status' => 'invalid-status',
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    /**
     * Test task update.
     */
    public function test_can_update_task(): void
    {
        $task = Task::create([
            'title' => 'Old Title',
            'description' => 'Old Description',
            'status' => TaskStatus::PENDING,
            'sort_order' => 1
        ]);

        $response = $this->put(route('tasks.update', $task), [
            'title' => 'New Title',
            'description' => 'New Description',
            'status' => TaskStatus::COMPLETED->value,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'New Title',
            'description' => 'New Description',
            'status' => TaskStatus::COMPLETED->value,
        ]);
    }

    /**
     * Test task deletion.
     */
    public function test_can_delete_task(): void
    {
        $task = Task::create([
            'title' => 'Delete Me',
            'status' => TaskStatus::PENDING,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * Test toggling task status via AJAX.
     */
    public function test_can_toggle_task_status_via_ajax(): void
    {
        $task = Task::create([
            'title' => 'Toggle Status Task',
            'status' => TaskStatus::PENDING,
        ]);

        $response = $this->patch(route('tasks.toggle-status', $task), [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'completed',
        ]);

        $this->assertEquals(TaskStatus::COMPLETED, $task->fresh()->status);
    }

    /**
     * Test reordering tasks via AJAX.
     */
    public function test_can_reorder_tasks_via_ajax(): void
    {
        $task1 = Task::create(['title' => 'Task 1', 'status' => TaskStatus::PENDING, 'sort_order' => 1]);
        $task2 = Task::create(['title' => 'Task 2', 'status' => TaskStatus::PENDING, 'sort_order' => 2]);

        $response = $this->post(route('tasks.reorder'), [
            'order' => [$task2->id, $task1->id]
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(1, $task2->fresh()->sort_order);
        $this->assertEquals(2, $task1->fresh()->sort_order);
    }
}
