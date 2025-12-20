<?php

namespace App\DataTables;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StudentsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->rawColumns(['user', 'application', 'created_at'])
            ->editColumn('user', function (Student $student) {
                return view('pages.admin.students.columns._user', compact('student'));
            })
            ->editColumn('application', function (Student $student) {
                return view('pages.admin.students.columns._application', compact('student'));
            })
            ->editColumn('created_at', function (Student $student) {
                return $student->created_at->format('d M Y, h:i a');
            })
            ->addColumn('action', function (Student $student) {
                return view('pages.admin.students.columns._actions', compact('student'));
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Student $model): QueryBuilder
    {
        $query = $model->newQuery()->with('studentApplication');

        // Filter by application status if provided
        $request = request();
        if ($request->has('application_status') && $request->get('application_status') !== '') {
            $status = $request->get('application_status');
            if ($status === 'with') {
                $query->whereHas('studentApplication');
            } elseif ($status === 'without') {
                $query->whereDoesntHave('studentApplication');
            }
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('students-table')
            ->columns($this->getColumns())
            ->minifiedAjax(url()->current())
            ->dom('rt'."<'row'<'col-sm-12'tr>><'d-flex justify-content-between'<'col-sm-12 col-md-5'i><'d-flex justify-content-between'p>>")
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(3)
            ->parameters([
                'pageLength' => 20,
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('user')->title('Name')->addClass('d-flex align-items-center')->name('first_name'),
            Column::make('email')->title('Email'),
            Column::make('application')->title('Application')->searchable(false)->orderable(false),
            Column::make('created_at')->title('Created Date')->addClass('text-nowrap'),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Students_'.date('YmdHis');
    }
}
