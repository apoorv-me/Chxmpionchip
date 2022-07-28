<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            // ->addColumn('Birth_date', function($row){
            //     if(isset($row['Birth_date'])){
            //         $date = date('m-d-Y',strtotime($row['Birth_date']));
            //         return $date;
            //     } 
            //  })
            ->addColumn('Image', function($row){
                if(isset($row['image_path'])){
                    $btn = '<img src="'.$row['image_path'].'">';
                    return $btn;
                } else {
                    $btn = 'N/A';
                    return $btn;
                }
                
             })
            ->addColumn('status', function($row){
            
               $btn = '<button class="btn btn-warning"  onclick="updateStatus('.$row['id'].')">'.$row['status'].'</button>';
                return $btn;
            })
            ->addColumn('delete', function($row){
            $btn = '<i class="fa fa-trash" aria-hidden="true" id="'.$row['id'].'" onclick="deleteUser('.$row['id'].')"></i>';
           // $btn = '<button class="btn btn-danger" id="'.$row['id'].'" onclick="deleteUser('.$row['id'].')">Delete</button>';

             return $btn;
            })
            ->rawColumns(['Image','status','delete']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('example')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            // Column::computed('action')
            //       ->exportable(false)
            //       ->printable(false)
            //       ->width(60)
            //       ->addClass('text-center'),
                  Column::make('name'),
                  Column::make('username'),
                  Column::make('email'),
                  //Column::make('Birth_date'),
                  Column::make('gender'),
                  //Column::make('phoneNumber'),
                  Column::make('Image'),
                  Column::make('status'),
                  Column::make('delete'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Users_' . date('YmdHis');
    }
}
