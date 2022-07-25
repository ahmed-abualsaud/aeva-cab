<?php /** @noinspection PhpMissingReturnTypeInspection */

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait BaseQueryCriteria
{
    public array $search;
    public string $main_table;
    public Builder $builder;

    public function __construct()
    {
        $this->search = $this->search();
        $this->builder = $this->applySearch();
    }

    /**
     * @return array
     */
    public function search() : array
    {
        return [
            'for'=> request()->query('search_for'),
            'value'=> request()->query('search_value'),
            'timestamp'=> $this->timestamp(),
            'dir'=> $this->orderByDir(),
            'order_by'=> request()->query('order_by') ?? $this->timestamp(),
        ];
    }


    /**
     * @return Builder
     */
    public function applySearch() : Builder
    {
        $this->builder = $this->applyOptionalSearchFilters($this->builder);
        $this->builder = $this->applyQueries($this->builder);
        return $this->builder;
    }

    /**
     * @param ...$args
     * @return Builder
     */
    public function applyQueries(...$args) : Builder
    {
        (!empty($this->search['for']) and ! is_null($this->search['value'])) and $this->builder = $this->builder->where($this->search['for'],'like','%'.$this->search['value'].'%');

        $search_args = collect($args)->except('search_for','search_value','timestamp','dir','date')->filter(fn($arg) => ! is_null(request()->query($arg)));
        $search_args->each(fn($param) => $this->builder = $this->builder->where($param,'like','%'.request()->query($param).'%'));
        return $this->builder->orderBy($this->search['order_by'],$this->search['dir']);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function applyOptionalSearchFilters(Builder $builder) : Builder
    {
        return $builder;
    }
    /**
     * @param Builder $builder
     * @param string|null $date_column
     * @param string $date_query_key
     * @return Builder
     */
    public function dateFilter(Builder $builder, string $date_column = null, string $date_query_key = 'date') : Builder
    {
        $date_column ??= $this->timestamp();
        $date_array = explode(',' ,request()->query($date_query_key));
        return count($date_array) == 1 ? $builder->whereDate($this->fullColumnName($date_column),$this->dbDate(head($date_array)))
            : $builder->whereBetween($this->fullColumnName($date_column),[$this->dbDate(head($date_array),'startOfDay'),$this->dbDate(last($date_array),'endOfDay')]);
    }


    /**
     * @param $date
     * @param string $carbon_method
     * @param array $carbon_method_args
     * @param string $format
     * @return mixed
     */
    public function dbDate($date, string $carbon_method = 'startOfDay', array $carbon_method_args = [], string $format = 'Y-m-d H:i:s')
    {
        try {
            return Carbon::parse($date)->{$carbon_method}(...$carbon_method_args)->format($format);
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * @param Builder $builder
     * @param string $column
     * @param string $operator
     * @param string|null $query_key
     * @return Builder
     */
    public function flagFilter(Builder $builder, string $column, string $operator = '=', string $query_key = null) : Builder
    {
        $query_array = explode(',' ,request()->query($query_key ?? $column));
        return count($query_array) == 1  ?  $builder->where($this->fullColumnName($column),$operator,head($query_array)) : $builder->whereIn($this->fullColumnName($column),$query_array);
    }

    /**
     * @param Builder $builder
     * @param string $column
     * @param string|null $query_key
     * @return Builder
     */
    public function actualOrRangeFilter(Builder $builder, string $column, string $query_key = null) : Builder
    {
        $query_array = explode(',' ,request()->query($query_key ?? $column));
        return count($query_array) == 1  ?  $builder->where($this->fullColumnName($column),head($query_array))
            : $builder->whereBetween($this->fullColumnName($column),[head($query_array),last($query_array)]);
    }

    /**
     * @param Builder $builder
     * @param string $query_key
     * @param string $method
     * @param array $method_args
     * @return Builder
     */
    public function applyOrSkip(Builder $builder, string $query_key, string $method, array $method_args = []) : Builder
    {
        return ! is_null(request()->query($query_key)) ? $this->{$method}(...$method_args) : $builder;
    }

    /**
     * probable column that will be used in order by including main_table timestamps
     * @param array $main_table_columns
     * @param array $probable
     * @param string $query_key
     * @return string
     */
    public function orderByProbability(array $main_table_columns, array $probable = [], string $query_key = 'order_by') : string
    {
        $order_by = request()->query($query_key);
        if (!empty($order_by)){
            $prepared = $this->prepareOrderBy($order_by,$main_table_columns);
            return in_array($prepared['order_by'],[...$prepared['main_table_columns'],...$probable]) ? $prepared['order_by'] : $this->createdAt();
        }
        return $this->createdAt();
    }

    /**
     * @param string $order_by
     * @param array $main_table_columns
     * @return array
     */
    private function prepareOrderBy(string $order_by, array $main_table_columns) : array
    {
        $table_columns = array_merge(array_combine($main_table_columns,$this->prepareMainTableColumns($main_table_columns)),$this->timestamps());
        $column = array_search($order_by,$table_columns);
        $column and $order_by = $table_columns[$column];
        in_array($order_by,array_keys($table_columns)) and $order_by = $table_columns[$order_by];
        return ['main_table_columns'=> array_values($table_columns),'order_by'=> $order_by];
    }

    private function prepareMainTableColumns(array $main_table_columns) : array
    {
        return array_map(fn($column) => $this->fullColumnName($column),$main_table_columns);
    }

    /**
     * declared timestamps keyed by generic others used as keys & values in preparing orderBy column to avoid ambiguous SQL error
     * @return string[]
     */
    private function timestamps() : array
    {
        return ['created_at' => $this->createdAt(),'updated_at'=> $this->updatedAt(),'deleted_at'=> $this->deletedAt()];
    }


    /**
     * @param string $created_at
     * @return string
     */
    public function createdAt(string $created_at = 'created_at') : string
    {
        return $this->fullColumnName($created_at);
    }

    /**
     * @param string $updated_at
     * @return string
     */
    public function updatedAt(string $updated_at = 'updated_at') : string
    {
        return $this->fullColumnName($updated_at);
    }

    /**
     * @param string $deleted_at
     * @return string
     */
    public function deletedAt(string $deleted_at = 'deleted_at') : string
    {
        return $this->fullColumnName($deleted_at);
    }

    /**
     * order by direction
     * @return string
     */
    private function orderByDir() : string
    {
        return request()->query('dir') == 'asc' ? 'asc' : 'desc';
    }

    /**
     * @param string $column_name
     * @return string
     */
    public function fullColumnName(string $column_name) : string
    {
        return $this->main_table.'.'.$column_name;
    }

    /**
     * @param string $timestamp_query_key
     * @return string
     */
    private function timestamp(string $timestamp_query_key = 'timestamp') : string
    {
        $time_stamp = request()->query($timestamp_query_key);
        return in_array($time_stamp,['created_at','updated_at','deleted_at']) ? $time_stamp : 'created_at';
    }
}
