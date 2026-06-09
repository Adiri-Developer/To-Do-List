<!-- Modal overlay -->
<div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <!-- Background overlay -->
        <div x-show="isModalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-500/75 transition-opacity dark:bg-gray-900/80" 
             @click="closeModal" aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="isModalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700 relative z-10">
            
            <form :action="modalMode === 'create' ? '{{ route('tasks.store') }}' : '/tasks/' + task.id" method="POST">
                @csrf
                <!-- Dynamic Method override for Edit -->
                <template x-if="modalMode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title" x-text="modalMode === 'view' ? 'Task Details' : (modalMode === 'create' ? 'Create New Task' : 'Edit Task')">
                            </h3>
                            <!-- View Mode -->
                            <template x-if="modalMode === 'view'">
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</h4>
                                        <p class="mt-1 text-base text-gray-900 dark:text-gray-100" x-text="task.title"></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h4>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap" x-text="task.description || 'No description provided.'"></p>
                                    </div>
                                    <div class="flex space-x-8">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Due Date</h4>
                                            <p class="mt-1 text-sm text-gray-800 dark:text-gray-200" x-text="task.due_date || '-'"></p>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h4>
                                            <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300" x-text="task.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Create/Edit Mode -->
                            <template x-if="modalMode !== 'view'">
                                <div class="mt-4 space-y-4">
                                    <!-- Title -->
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="title" x-model="task.title" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                        <textarea name="description" id="description" rows="3" x-model="task.description" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"></textarea>
                                    </div>

                                    <!-- Due Date -->
                                    <div>
                                        <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                                        <input type="date" name="due_date" id="due_date" x-model="task.due_date" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <template x-if="modalMode !== 'view'">
                        <button type="submit" class="cursor-pointer w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                            <span x-text="modalMode === 'create' ? 'Save Task' : 'Update Task'"></span>
                        </button>
                    </template>
                    <button type="button" @click="closeModal" class="cursor-pointer mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                        <span x-text="modalMode === 'view' ? 'Close' : 'Cancel'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
