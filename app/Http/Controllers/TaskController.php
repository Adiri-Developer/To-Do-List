<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = auth()->user()->tasks()->orderBy('due_date', 'asc')->get();
        
        $backlog = $tasks->where('status', 'backlog');
        $inProgress = $tasks->where('status', 'in_progress');
        $history = $tasks->where('status', 'completed');

        $calendarEvents = $tasks->map(function ($task) {
            $color = '#3B82F6'; // Default Blue (Backlog)
            if ($task->status === 'completed') {
                $color = '#10B981'; // Green
            } elseif ($task->status === 'in_progress') {
                $color = '#F59E0B'; // Amber
            }

            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->due_date,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'allDay' => true,
            ];
        })->values();

        $stats = [
            'backlog' => $backlog->count(),
            'in_progress' => $inProgress->count(),
            'completed' => $history->count(),
        ];

        return view('tasks.index', compact('backlog', 'inProgress', 'history', 'calendarEvents', 'stats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $validated['status'] = 'backlog';

        auth()->user()->tasks()->create($validated);

        return back()->with('success', 'Task created successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update($validated);

        return back()->with('success', 'Task updated successfully!');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:backlog,in_progress,completed',
        ]);

        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Status updated']);
        }

        return back()->with('success', 'Task status updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return back()->with('success', 'Task deleted successfully!');
    }

    /**
     * Export tasks to Excel.
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string|in:ALL,backlog,in_progress,completed',
            'format' => 'nullable|string|in:excel,json',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status', 'ALL');
        $format = $request->input('format', 'excel');

        if ($format === 'json') {
            $exportClass = new \App\Exports\TasksExport($startDate, $endDate, $status);
            $tasks = $exportClass->query()->get();
            
            $mappedTasks = $tasks->map(function ($task) use ($exportClass) {
                $mapped = $exportClass->map($task);
                return [
                    'Title' => $mapped[0],
                    'Description' => $mapped[1],
                    'Due Date' => $mapped[2],
                    'Status' => $mapped[3],
                ];
            });

            return response()->streamDownload(function () use ($mappedTasks) {
                echo json_encode($mappedTasks, JSON_PRETTY_PRINT);
            }, 'tasks.json', [
                'Content-Type' => 'application/json',
            ]);
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TasksExport($startDate, $endDate, $status), 
            'tasks.xlsx'
        );
    }
}
