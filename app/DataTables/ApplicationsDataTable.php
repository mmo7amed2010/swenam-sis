<?php

namespace App\DataTables;

use App\Models\StudentApplication;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

/**
 * @deprecated This class is no longer used. Applications now use the
 * HandlesDataTableRequests trait pattern in ApplicationReviewController.
 * See MODULE-ARCHITECTURE-BLUEPRINT.md for the new standard.
 *
 * This file is kept for reference but can be removed in the future.
 */
class ApplicationsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', function (StudentApplication $application) {
                $url = route('admin.applications.show', $application->id);

                return '<a href="'.$url.'" class="text-primary fw-semibold">'.
                       e($application->full_name).
                       '</a>';
            })
            ->addColumn('program_name', function (StudentApplication $application) {
                return $application->program_name ?? 'N/A';
            })
            ->addColumn('status_badge', function (StudentApplication $application) {
                return $this->getStatusBadge($application->status);
            })
            ->addColumn('actions', function (StudentApplication $application) {
                $url = route('admin.applications.show', $application->id);

                return '<a href="'.$url.'" class="btn btn-sm btn-light btn-active-light-primary">
                        <i class="bi bi-eye"></i> View
                        </a>';
            })
            ->editColumn('created_at', function (StudentApplication $application) {
                return $application->created_at ? $application->created_at->format('M d, Y H:i') : 'N/A';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
            })
            // Note: Program filtering by name not available since programs are fetched from LMS API
            ->filterColumn('program_name', function ($query, $keyword) {
                // Cannot filter by program name directly - data comes from external API
            })
            ->rawColumns(['name', 'status_badge', 'actions'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(StudentApplication $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['reviewer'])
            ->select('student_applications.*');

        // Apply filters from request
        $status = request('status');
        $from = request('from');
        $to = request('to');

        if ($status && $status !== 'all') {
            $query->byStatus($status);
        }

        if ($from && $to) {
            $query->byDateRange($from, $to);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('applications-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(4, 'desc') // Order by created_at column
            ->pageLength(25)
            ->lengthMenu([[25, 50, 100, -1], [25, 50, 100, 'All']])
            ->responsive(true)
            ->autoWidth(false)
            ->processing(true)
            ->serverSide(true)
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => [],
                'language' => [
                    'processing' => '<i class="fas fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                ],
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('name')
                ->title('Applicant Name')
                ->orderable(false)
                ->searchable(true),
            Column::make('email')
                ->title('Email')
                ->searchable(true),
            Column::make('program_name')
                ->title('Program')
                ->orderable(false)
                ->searchable(true),
            Column::make('created_at')
                ->title('Submitted Date')
                ->searchable(false),
            Column::make('status_badge')
                ->title('Status')
                ->orderable(true)
                ->searchable(false)
                ->name('status'),
            Column::computed('actions')
                ->title('Actions')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Applications_'.date('YmdHis');
    }

    /**
     * Get status badge HTML.
     */
    private function getStatusBadge(string $status): string
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'approved' => '<span class="badge badge-success">Approved</span>',
            'rejected' => '<span class="badge badge-danger">Rejected</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }
}
