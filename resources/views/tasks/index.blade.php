@extends('layouts.app')

@section('content')
<div x-data="taskManager()">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center px-4 sm:px-0 gap-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">My Tasks</h1>
        <div class="flex items-center space-x-4">
            <button @click="isExportModalOpen = true" class="cursor-pointer bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition-colors duration-150 ease-in-out flex items-center">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button>
            <button @click="openModal('create')" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition-colors duration-150 ease-in-out flex items-center">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Task
            </button>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="cursor-pointer text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2">
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

    <!-- Export Modal -->
    <div x-show="isExportModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="isExportModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto" @click="isExportModalOpen = false">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="isExportModalOpen" @click.stop x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <form action="{{ route('tasks.export') }}" method="GET">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4" id="modal-title">
                            Export Tasks to Excel
                        </h3>
                        <div class="space-y-4 text-left">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date (Optional)</label>
                                <input type="date" name="start_date" id="start_date" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date (Optional)</label>
                                <input type="date" name="end_date" id="end_date" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                                    <option value="ALL">All Statuses</option>
                                    <option value="backlog">Backlog</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="isExportModalOpen = false" type="submit" name="format" value="excel" class="cursor-pointer w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Download Excel
                        </button>
                        <button @click="isExportModalOpen = false" type="submit" name="format" value="json" class="cursor-pointer mt-3 sm:mt-0 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Download JSON
                        </button>
                        <button @click="isExportModalOpen = false" type="button" class="cursor-pointer mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
            isExportModalOpen: false,
            modalMode: 'create',
            task: {
                id: null,
                title: '',
                description: '',
                due_date: '',
                status: 'backlog',
                attachment_url: null
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
