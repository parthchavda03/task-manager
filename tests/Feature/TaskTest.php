<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest is redirected to login page when accessing tasks.
     */
    public function test_guest_is_redirected_to_login_when_accessing_tasks(): void
    {
        $response = $this->get(route('tasks.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test login page loads successfully.
     */
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Welcome to TaskSpace');
    }

    /**
     * Test logging in with correct credentials.
     */
    public function test_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@gmail.com',
            'password' => '123456',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test logging in with incorrect credentials.
     */
    public function test_cannot_login_with_incorrect_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test logging out.
     */
    public function test_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Test home page redirects to tasks index if authenticated.
     */
    public function test_authenticated_home_redirects_to_tasks_index(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect(route('tasks.index'));
    }

    /**
     * Test tasks index page loads successfully when authenticated.
     */
    public function test_tasks_index_loads_successfully_when_authenticated(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('tasks.index'));
        $response->assertStatus(200);
        $response->assertSee('TaskSpace');
    }

    /**
     * Test task creation validation and storage.
     */
    public function test_can_create_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
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
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
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
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
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
        $user = User::factory()->create();

        $task = Task::create([
            'title' => 'Old Title',
            'description' => 'Old Description',
            'status' => TaskStatus::PENDING,
            'sort_order' => 1
        ]);

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
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
        $user = User::factory()->create();

        $task = Task::create([
            'title' => 'Delete Me',
            'status' => TaskStatus::PENDING,
        ]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * Test toggling task status via AJAX.
     */
    public function test_can_toggle_task_status_via_ajax(): void
    {
        $user = User::factory()->create();

        $task = Task::create([
            'title' => 'Toggle Status Task',
            'status' => TaskStatus::PENDING,
        ]);

        $response = $this->actingAs($user)->patch(route('tasks.toggle-status', $task), [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

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
        $user = User::factory()->create();

        $task1 = Task::create(['title' => 'Task 1', 'status' => TaskStatus::PENDING, 'sort_order' => 1]);
        $task2 = Task::create(['title' => 'Task 2', 'status' => TaskStatus::PENDING, 'sort_order' => 2]);

        $response = $this->actingAs($user)->post(route('tasks.reorder'), [
            'order' => [$task2->id, $task1->id]
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(1, $task2->fresh()->sort_order);
        $this->assertEquals(2, $task1->fresh()->sort_order);
    }
}
