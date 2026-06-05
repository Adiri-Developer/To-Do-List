@props(['task'])

<div data-id="{{ $task->id }}" @dblclick="openModal('view', {{ json_encode($task) }})" class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-200 group relative cursor-grab active:cursor-grabbing">
    <!-- Drag Handle Area -->
    <div class="drag-handle absolute inset-0 z-0"></div>
    
    <div class="flex items-start justify-between relative z-10 pointer-events-none">
        <div class="flex-1 pr-4">
            <h3 class="task-title text-base font-medium text-gray-900 dark:text-gray-100 {{ $task->status === 'completed' ? 'line-through text-gray-400 dark:text-gray-500' : '' }}">
                {{ $task->title }}
            </h3>
            @if($task->description)
                <p class="task-desc mt-1 text-sm text-gray-500 dark:text-gray-400 {{ $task->status === 'completed' ? 'line-through' : '' }}">
                    {{ Str::limit($task->description, 60) }}
                </p>
            @endif
            
            <div class="mt-3 flex flex-wrap gap-2 items-center text-xs text-gray-500 dark:text-gray-400">
                @if($task->due_date)
                    <div class="flex items-center">
                        <svg class="mr-1 h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <time datetime="{{ $task->due_date }}">
                            {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                        </time>
                    </div>
                @endif
                <span class="task-status-badge inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-auto">
            <!-- Edit Button (Alpine logic) -->
            <button @click.stop="openModal('edit', {{ json_encode($task) }})" class="p-1 rounded-full text-gray-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 focus:outline-none transition-colors" title="Edit Task">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>

            <!-- Delete Form -->
            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                @csrf
                @method('DELETE')
                <button type="submit" @click.stop class="p-1 rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 focus:outline-none transition-colors" title="Delete Task">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
