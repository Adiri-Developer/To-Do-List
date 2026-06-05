import './bootstrap';
import Alpine from 'alpinejs';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import Chart from 'chart.js/auto';
import Sortable from 'sortablejs';

window.Alpine = Alpine;
Alpine.start();

// Calendar Initialization
window.renderFullCalendar = function() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl || calendarEl.innerHTML !== '') return;
    
    const events = window.taskCalendarEvents || [];
    
    window.myCalendar = new Calendar(calendarEl, {
        plugins: [ dayGridPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek'
        },
        events: events,
        height: 'auto'
    });
    
    window.myCalendar.render();
};

// Chart.js Initialization
window.renderTaskChart = function() {
    const ctx = document.getElementById('taskChart');
    if (!ctx || ctx.classList.contains('rendered')) return;
    
    const stats = window.taskStats || { backlog: 0, in_progress: 0, completed: 0 };
    
    window.myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Backlog', 'In Progress', 'Completed'],
            datasets: [{
                data: [stats.backlog, stats.in_progress, stats.completed],
                backgroundColor: [
                    '#3B82F6', // Blue-500
                    '#F59E0B', // Amber-500
                    '#10B981'  // Emerald-500
                ],
                hoverBackgroundColor: [
                    '#2563EB',
                    '#D97706',
                    '#059669'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: document.documentElement.classList.contains('dark') ? '#D1D5DB' : '#374151'
                    }
                }
            }
        }
    });
    
    ctx.classList.add('rendered');
};

// Kanban / Sortable Initialization
window.initKanban = function() {
    const columns = document.querySelectorAll('.kanban-column');
    
    columns.forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'opacity-50',
            handle: '.drag-handle', // we will add a drag handle class
            onEnd: function(evt) {
                const itemEl = evt.item;
                const newStatus = evt.to.getAttribute('data-status');
                const taskId = itemEl.getAttribute('data-id');
                
                // If it wasn't moved to a new column, do nothing
                if (evt.from === evt.to) return;
                
                // Optimistically update the UI badge
                const badge = itemEl.querySelector('.task-status-badge');
                if (badge) {
                    const formattedStatus = newStatus.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                    badge.textContent = formattedStatus;
                }

                // Update counter numbers
                const fromColumn = evt.from.closest('.bg-gray-50');
                const toColumn = evt.to.closest('.bg-gray-50');
                
                if (fromColumn && toColumn) {
                    const fromCounter = fromColumn.querySelector('.kanban-counter');
                    const toCounter = toColumn.querySelector('.kanban-counter');
                    if (fromCounter) fromCounter.textContent = parseInt(fromCounter.textContent) - 1;
                    if (toCounter) toCounter.textContent = parseInt(toCounter.textContent) + 1;
                }

                // Update strikethrough for completed
                const title = itemEl.querySelector('.task-title');
                const desc = itemEl.querySelector('.task-desc');
                
                if (newStatus === 'completed') {
                    if (title) title.classList.add('line-through', 'text-gray-400', 'dark:text-gray-500');
                    if (desc) desc.classList.add('line-through');
                } else {
                    if (title) title.classList.remove('line-through', 'text-gray-400', 'dark:text-gray-500');
                    if (desc) desc.classList.remove('line-through');
                }

                // Update Analytics Summary Numbers
                const fromStatus = evt.from.getAttribute('data-status');
                const summaryFrom = document.getElementById('summary-' + fromStatus);
                const summaryTo = document.getElementById('summary-' + newStatus);
                if (summaryFrom) summaryFrom.textContent = parseInt(summaryFrom.textContent) - 1;
                if (summaryTo) summaryTo.textContent = parseInt(summaryTo.textContent) + 1;

                // Update Chart
                if (window.myChart) {
                    const statusIndexMap = { 'backlog': 0, 'in_progress': 1, 'completed': 2 };
                    window.myChart.data.datasets[0].data[statusIndexMap[fromStatus]]--;
                    window.myChart.data.datasets[0].data[statusIndexMap[newStatus]]++;
                    window.myChart.update();
                }

                // Update Calendar Event
                if (window.myCalendar) {
                    const calendarEvent = window.myCalendar.getEventById(taskId);
                    if (calendarEvent) {
                        let newColor = '#3B82F6'; // Blue
                        if (newStatus === 'completed') newColor = '#10B981'; // Green
                        else if (newStatus === 'in_progress') newColor = '#F59E0B'; // Amber
                        
                        calendarEvent.setProp('backgroundColor', newColor);
                        calendarEvent.setProp('borderColor', newColor);
                    }
                }

                // Send AJAX request to update status
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                }).then(response => response.json())
                  .then(data => {
                      if(!data.success) {
                          console.error('Failed to update status');
                          // Revert UI if needed (for simplicity, we assume success here, but in production we'd revert)
                      }
                  }).catch(error => {
                      console.error('Error updating status:', error);
                  });
            }
        });
    });
};
