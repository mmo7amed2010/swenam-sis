<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersAssignedRoleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->rawColumns(['user'])
            ->editColumn('user', function (User $user) {
                return view('pages.admin.user-management.users.columns._user', compact('user'));
            })
            ->editColumn('created_at', function (User $user) {
                return $user->created_at->format('d M Y, h:i a');
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('usersAssignedRole-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt'."<'row'<'col-sm-12'tr>><'d-flex justify-content-between'<'col-sm-12 col-md-5'i><'d-flex justify-content-between'p>>")
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(1)
            ->drawCallback('function() {'.file_get_contents(resource_path('views/pages/apps/user-management/users/columns/_draw-scripts.js')).'}');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('user')->addClass('d-flex align-items-center')->name('name'),
            Column::make('created_at')->title(__('Joined Date'))->addClass('text-nowrap'),
        ];
    }
}
