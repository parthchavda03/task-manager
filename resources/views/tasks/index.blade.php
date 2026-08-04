@extends('layouts.app')

@section('title', 'Tasks Dashboard - TaskSpace')



@section('content')
<div class="row">
    <!-- Filters and Dashboard Summary -->
    <div class="col-12 col-lg-4 mb-4">
        <!-- Dashboard Summary Card -->
        <div class="glass-card p-4 mb-4">
            <h5 class="font-weight-bold mb-4 text-white">Summary</h5>
            <div class="row text-center">
                <div class="col-6 mb-3">
                    <div class="p-3 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border);">
                        <small class="text-secondary d-block uppercase tracking-wider mb-1" style="font-size: 0.65rem;">Total Tasks</small>
                        <h3 class="m-0 font-weight-bold text-white">{{ $tasks->total() }}</h3>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="p-3 rounded" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.15);">
                        <small class="text-secondary d-block uppercase tracking-wider mb-1" style="font-size: 0.65rem; color: #34d399 !important;">Completed</small>
                        <h3 class="m-0 font-weight-bold" style="color: #10b981;">{{ \App\Models\Task::where('status', \App\Enums\TaskStatus::COMPLETED)->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-premium btn-block py-2" data-toggle="modal" data-target="#createTaskModal">
                    <i class="fa-solid fa-plus mr-2"></i> Create New Task
                </button>
            </div>
        </div>

        <!-- Search Card -->
        <div class="glass-card p-4">
            <h5 class="font-weight-bold mb-3 text-white">Search Tasks</h5>
            <form action="{{ route('tasks.index') }}" method="GET">
                <div class="form-group mb-3 position-relative">
                    <input type="text" name="search" class="form-control glass-input w-100 pr-5" placeholder="Search by title or description..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('tasks.index') }}" class="position-absolute text-muted" style="right: 15px; top: 50%; transform: translateY(-50%); z-index: 10;" title="Clear Search">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-premium btn-block py-2">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Search
                </button>
            </form>
        </div>
    </div>

    <!-- Tasks List Area -->
    <div class="col-12 col-lg-8">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="font-weight-bold m-0 text-white">Task List</h5>
                    <small class="text-secondary">Drag handles <i class="fa-solid fa-grip-vertical mx-1"></i> to reorder tasks</small>
                </div>
                <span class="badge badge-pill py-2 px-3" style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); color: var(--text-secondary); font-size: 0.75rem;">
                    Page {{ $tasks->currentPage() }} of {{ $tasks->lastPage() ?: 1 }}
                </span>
            </div>

            <!-- Draggable Container -->
            <div id="tasks-list" class="list-group">
                @forelse($tasks as $task)
                    <div class="list-group-item task-item glass-card p-3 mb-3 d-flex align-items-center justify-content-between {{ $task->status === \App\Enums\TaskStatus::COMPLETED ? 'task-completed' : '' }}" 
                         data-id="{{ $task->id }}">
                        
                        <div class="d-flex align-items-center flex-grow-1 mr-3">
                            <!-- Drag Handle -->
                            <div class="drag-handle mr-3" title="Drag to reorder">
                                <i class="fa-solid fa-grip-vertical"></i>
                            </div>

                            <!-- Task Info -->
                            <div class="flex-grow-1">
                                <h6 class="task-title m-0 font-weight-bold text-white transition-all">{{ $task->title }}</h6>
                                @if($task->description)
                                    <p class="task-desc m-0 mt-1 text-secondary" style="font-size: 0.85rem; line-height: 1.4;">
                                        {{ Str::limit($task->description, 80) }}
                                    </p>
                                @endif
                                <div class="mt-2 d-flex align-items-center" style="font-size: 0.7rem; color: var(--text-secondary);">
                                    <i class="fa-regular fa-calendar-days mr-1"></i>
                                    <span>Created {{ $task->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge and Actions -->
                        <div class="d-flex align-items-center text-right">
                            <span class="status-badge mr-3 {{ $task->status === \App\Enums\TaskStatus::COMPLETED ? 'status-completed' : 'status-pending' }}" 
                                  data-id="{{ $task->id }}">
                                <i class="fa-solid {{ $task->status === \App\Enums\TaskStatus::COMPLETED ? 'fa-circle-check' : 'fa-circle' }}"></i>
                                <span class="badge-text">{{ $task->status === \App\Enums\TaskStatus::COMPLETED ? 'Completed' : 'Pending' }}</span>
                            </span>

                            <div class="d-flex">
                                <!-- Edit Button -->
                                <button type="button" class="action-btn btn-edit mr-2" 
                                        data-toggle="modal" 
                                        data-target="#editTaskModal"
                                        data-id="{{ $task->id }}"
                                        data-title="{{ $task->title }}"
                                        data-description="{{ $task->description }}"
                                        data-status="{{ $task->status->value }}"
                                        title="Edit Task">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="action-btn btn-delete" 
                                        data-toggle="modal" 
                                        data-target="#deleteTaskModal"
                                        data-id="{{ $task->id }}"
                                        title="Delete Task">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 rounded" style="background: rgba(255,255,255,0.01); border: 1px dashed var(--glass-border);">
                        <i class="fa-solid fa-list-check text-muted mb-3" style="font-size: 2.5rem; opacity: 0.4;"></i>
                        <h6 class="text-secondary font-weight-bold">No tasks found</h6>
                        <p class="text-muted m-0" style="font-size: 0.85rem;">Try creating a new task or adjusting your search filter.</p>
                    </div>
                @endforelse
            </div>

            <!-- Laravel Pagination Links -->
            <div class="mt-4">
                {{ $tasks->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- ================= CREATE TASK MODAL ================= -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="createTaskModalLabel">
                    <i class="fa-solid fa-circle-plus text-indigo mr-2" style="color: #6366f1;"></i> Create Task
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="create_title" class="font-weight-bold text-white small">Title <span class="text-danger">*</span></label>
                        <input type="text" id="create_title" name="title" class="form-control glass-input" required maxlength="255" placeholder="Enter task title...">
                    </div>
                    <div class="form-group mb-3">
                        <label for="create_description" class="font-weight-bold text-white small">Description</label>
                        <textarea id="create_description" name="description" rows="4" class="form-control glass-input" placeholder="Enter task description (optional)..."></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label for="create_status" class="font-weight-bold text-white small">Status <span class="text-danger">*</span></label>
                        <select id="create_status" name="status" class="form-control glass-input" required>
                            <option value="pending" selected>Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-glass-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-premium">Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= EDIT TASK MODAL ================= -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="editTaskModalLabel">
                    <i class="fa-solid fa-pen-to-square text-indigo mr-2" style="color: #6366f1;"></i> Edit Task
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTaskForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_title" class="font-weight-bold text-white small">Title <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title" name="title" class="form-control glass-input" required maxlength="255" placeholder="Enter task title...">
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_description" class="font-weight-bold text-white small">Description</label>
                        <textarea id="edit_description" name="description" rows="4" class="form-control glass-input" placeholder="Enter task description (optional)..."></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label for="edit_status" class="font-weight-bold text-white small">Status <span class="text-danger">*</span></label>
                        <select id="edit_status" name="status" class="form-control glass-input" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-glass-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-premium">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= DELETE CONFIRMATION MODAL ================= -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteTaskForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center pt-0">
                    <div class="mb-3" style="color: #ef4444; font-size: 3rem;">
                        <i class="fa-regular fa-circle-xmark"></i>
                    </div>
                    <h5 class="font-weight-bold text-white mb-2">Delete Task?</h5>
                    <p class="text-secondary small">Are you sure you want to delete this task? This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-glass-secondary py-2 px-3 mr-2" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-premium bg-danger py-2 px-3" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SortableJS -->
<script src="{{ asset('js/sortable.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Setup AJAX requests to contain CSRF header
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 1. AJAX Task Status Toggling
        $('.status-badge').on('click', function(e) {
            e.preventDefault();
            const badge = $(this);
            const taskId = badge.data('id');
            const item = badge.closest('.task-item');
            
            // Toggle effect visually (optimistic UI update)
            badge.css('pointer-events', 'none'); // Prevent double click
            
            $.ajax({
                url: `/tasks/${taskId}/toggle-status`,
                type: 'PATCH',
                dataType: 'json',
                success: function(response) {
                    badge.css('pointer-events', 'auto');
                    if (response.success) {
                        const icon = badge.find('i');
                        const text = badge.find('.badge-text');
                        
                        if (response.status === 'completed') {
                            badge.removeClass('status-pending').addClass('status-completed');
                            icon.removeClass('fa-circle').addClass('fa-circle-check');
                            text.text('Completed');
                            item.addClass('task-completed');
                        } else {
                            badge.removeClass('status-completed').addClass('status-pending');
                            icon.removeClass('fa-circle-check').addClass('fa-circle');
                            text.text('Pending');
                            item.removeClass('task-completed');
                        }
                        
                        showToast(response.message, 'success');
                    } else {
                        showToast('Error updating status.', 'danger');
                    }
                },
                error: function(xhr) {
                    badge.css('pointer-events', 'auto');
                    showToast('Failed to toggle status.', 'danger');
                    console.error(xhr.responseText);
                }
            });
        });

        // 2. SortableJS Drag & Drop List Initialization
        const el = document.getElementById('tasks-list');
        if (el) {
            new Sortable(el, {
                handle: '.drag-handle',
                animation: 250,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    // Collect task IDs in new order
                    const order = [];
                    $('#tasks-list .task-item').each(function() {
                        order.push($(this).data('id'));
                    });

                    // Send AJAX reorder request to Laravel backend
                    $.ajax({
                        url: '{{ route("tasks.reorder") }}',
                        type: 'POST',
                        data: { order: order },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showToast(response.message, 'success');
                            } else {
                                showToast('Error updating list order.', 'danger');
                            }
                        },
                        error: function(xhr) {
                            showToast('Failed to update task order.', 'danger');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        }

        // 3. Edit Modal Populating
        $('#editTaskModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const title = button.data('title');
            const description = button.data('description');
            const status = button.data('status');
            
            const modal = $(this);
            modal.find('#editTaskForm').attr('action', `/tasks/${id}`);
            modal.find('#edit_title').val(title);
            modal.find('#edit_description').val(description);
            modal.find('#edit_status').val(status);
        });

        // 4. Delete Modal Populating
        $('#deleteTaskModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            
            const modal = $(this);
            modal.find('#deleteTaskForm').attr('action', `/tasks/${id}`);
        });
    });
</script>
@endsection
