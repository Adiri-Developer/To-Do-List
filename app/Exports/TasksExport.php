<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Database\Eloquent\Builder;

class TasksExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function query()
    {
        $query = Task::where('user_id', auth()->id());

        if ($this->startDate) {
            $query->whereDate('due_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('due_date', '<=', $this->endDate);
        }

        if ($this->status && $this->status !== 'ALL') {
            $query->where('status', $this->status);
        }

        return $query->orderBy('due_date', 'asc');
    }

    public function headings(): array
    {
        return [
            'Title',
            'Description',
            'Due Date',
            'Status',
            'Attachment',
        ];
    }

    public function map($task): array
    {
        // Convert internal status to human readable for export mapping
        $statusMap = [
            'backlog' => 'Backlog',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ];

        return [
            $task->title,
            $task->description,
            $task->due_date ? date('Y-m-d', strtotime($task->due_date)) : '',
            $statusMap[$task->status] ?? $task->status,
            $task->attachment_url
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Determine how many rows we have roughly (e.g., up to row 1000 for validation)
                // D column is the Status column (Title=A, Description=B, Due Date=C, Status=D)
                $validation = $event->sheet->getCell('D2')->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a status from the drop-down list.');
                $validation->setFormula1('"Backlog,In Progress,Completed"');

                // Apply to range
                $event->sheet->setDataValidation('D2:D1048576', $validation);
                
                // Auto-size columns for better readability
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setAutoSize(true);
            },
        ];
    }
}
