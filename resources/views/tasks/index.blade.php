@extends('layouts.app')

@section('content')
<div x-data="taskManager()">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center px-4 sm:px-0 gap-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">My Tasks</h1>
        <div class="flex items-center space-x-4">
            <button @click="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition-colors duration-150 ease-in-out flex items-center">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Task
            </button>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2">
                    Log Out
                </button>
            </form>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-0">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'list'" :class="{'border-blue-500 text-blue-600 dark:text-blue-500': activeTab === 'list', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'list'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                Board View
            </button>
            <button @click="activeTab = 'calendar'; renderCalendar();" :class="{'border-blue-500 text-blue-600 dark:text-blue-500': activeTab === 'calendar', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'calendar'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                Calendar View
            </button>
            <button @click="activeTab = 'analytics'; renderChart();" :class="{'border-blue-500 text-blue-600 dark:text-blue-500': activeTab === 'analytics', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'analytics'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                Analytics
            </button>
        </nav>
    </div>

    <!-- Error/Success Messages -->
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 sm:mx-0" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 sm:mx-0" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TAB: LIST (BOARD) VIEW -->
    <div x-show="activeTab === 'list'" class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 sm:px-0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0">
        
        <!-- Backlog Section -->
        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Backlog</h2>
                <span class="kanban-counter bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">{{ $backlog->count() }}</span>
            </div>
            
            <div class="space-y-3 min-h-[200px] kanban-column" data-status="backlog">
                @foreach($backlog as $task)
                    <x-task-card :task="$task" />
                @endforeach
            </div>
        </div>

        <!-- In Progress Section -->
        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">In Progress</h2>
                <span class="kanban-counter bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-amber-900/50 dark:text-amber-300">{{ $inProgress->count() }}</span>
            </div>
            
            <div class="space-y-3 min-h-[200px] kanban-column" data-status="in_progress">
                @foreach($inProgress as $task)
                    <x-task-card :task="$task" />
                @endforeach
            </div>
        </div>

        <!-- History / Completed Section -->
        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Completed</h2>
                <span class="kanban-counter bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900/50 dark:text-green-300">{{ $history->count() }}</span>
            </div>

            <div class="space-y-3 min-h-[200px] kanban-column opacity-90" data-status="completed">
                @foreach($history as $task)
                    <x-task-card :task="$task" />
                @endforeach
            </div>
        </div>
    </div>

    <!-- TAB: CALENDAR VIEW -->
    <div x-show="activeTab === 'calendar'" style="display: none;" class="px-4 sm:px-0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0">
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div id="calendar" class="dark:text-gray-200"></div>
        </div>
    </div>

    <!-- TAB: ANALYTICS VIEW -->
    <div x-show="activeTab === 'analytics'" style="display: none;" class="px-4 sm:px-0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 flex flex-col items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-6 text-center">Task Completion Ratio</h2>
                <div class="w-full max-w-xs aspect-square">
                    <canvas id="taskChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Summary</h2>
                <div class="space-y-4">
                    <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Backlog</p>
                        <p id="summary-backlog" class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $stats['backlog'] }}</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/30 p-4 rounded-lg">
                        <p class="text-sm font-medium text-amber-600 dark:text-amber-400">In Progress</p>
                        <p id="summary-in_progress" class="text-3xl font-bold text-amber-900 dark:text-amber-100">{{ $stats['in_progress'] }}</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg">
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Total Completed</p>
                        <p id="summary-completed" class="text-3xl font-bold text-green-900 dark:text-green-100">{{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Component -->
    <x-task-modal />
</div>

<!-- Scripts for Calendar and Chart -->
<script>
    window.taskCalendarEvents = @json($calendarEvents);
    window.taskStats = @json($stats);
</script>

<!-- Alpine.js logic -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('taskManager', () => ({
            activeTab: 'list',
            isModalOpen: false,
            modalMode: 'create',
            task: {
                id: null,
                title: '',
                description: '',
                due_date: '',
                status: 'backlog'
            },
            calendarRendered: false,
            chartRendered: false,
            
            init() {
                // Initialize Kanban when Alpine is ready
                setTimeout(() => {
                    if (window.initKanban) {
                        window.initKanban();
                    }
                }, 200);
            },
            
            openModal(mode, taskData = null) {
                this.modalMode = mode;
                if ((mode === 'edit' || mode === 'view') && taskData) {
                    this.task = { ...taskData };
                    if (this.task.due_date) {
                        this.task.due_date = this.task.due_date.split(' ')[0]; 
                    }
                } else {
                    this.resetTask();
                }
                this.isModalOpen = true;
            },
            
            closeModal() {
                this.isModalOpen = false;
                setTimeout(() => this.resetTask(), 200);
            },
            
            resetTask() {
                this.task = {
                    id: null,
                    title: '',
                    description: '',
                    due_date: '',
                    status: 'backlog'
                };
            },
            
            renderCalendar() {
                setTimeout(() => {
                    if (!this.calendarRendered) {
                        if (window.renderFullCalendar) {
                            window.renderFullCalendar();
                            this.calendarRendered = true;
                        }
                    } else if (window.myCalendar) {
                        window.myCalendar.updateSize();
                    }
                }, 100); 
            },
            
            renderChart() {
                if(this.chartRendered) return;
                
                setTimeout(() => {
                    if (window.renderTaskChart) {
                        window.renderTaskChart();
                        this.chartRendered = true;
                    }
                }, 100);
            }
        }));
    });
</script>
@endsection
