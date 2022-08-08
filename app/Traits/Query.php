<?php /** @noinspection PhpUnnecessaryLocalVariableInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\ArrayShape;

trait Query
{
    public static array $booleans = ['true','false','1','0',true,false,0,1,'on','off','yes','no'];

    /*
    public static array $search;
    public static string $main_table;
    public static Builder $builder;
    public static array $filters;
    */

    abstract public static function mainTable() : string;
    abstract public static function builder() : Builder;
    abstract public static function filters() : array;


    /**
     * @return array
     */
    #[ArrayShape(['for' => "array|null|string", 'value' => "array|null|string", 'timestamp' => "string", 'dir' => "string", 'order_by' => "array|string"])]
    public static function searchParameters() : array
    {
        return [
            'for'=> request()->query('search_for'),
            'value'=> request()->query('search_value'),
            'timestamp'=> static::timestamp(),
            'dir'=> static::orderByDir(),
            'order_by'=> request()->query('order_by') ?? static::timestamp(),
        ];
    }

    public static function applyBooleanFilter(Builder $builder,?string $query_parameter,string $column,string $separator = ',')
    {
        if (empty_graph_ql_value($query_parameter)) return $builder;
        $query_array = explode($separator,$query_parameter);
        $query_values = collect($query_array)->transform(function ($value){
            empty_graph_ql_value($value) and $value = null;
            in_array($value,static::$booleans,true) and $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            return $value;
        })->all();
        $builder = count($query_values) == 1 ? $builder->where($column,head($query_values)) : $builder->whereIn($column,$query_values);
        return $builder;
    }

    public static function applySearch(Builder $builder = null) : Builder
    {
       $builder ??= static::builder();
       $builder = static::accToTrash($builder);
       $search = static::searchParameters();

        (!empty($search['for']) and ! is_null($search['value'])) and $builder = $builder->where($search['for'],'like','%'.$search['value'].'%');
        $builder = static::applyDateFilter($builder);

        $operators_keyed_by_column = collect(static::filters());
        $searched_columns = $operators_keyed_by_column->reject(fn($operator,$column_name) => ! static::isAcceptedValue(request()->query($column_name)) or is_null(request()->query($column_name)));
        $search_args = $operators_keyed_by_column->intersectByKeys($searched_columns);

        $search_args->each(function ($operator,$column_name) use ($builder){
            $query_params = explode(',',request()->query($column_name));
            if (count($query_params) == 1)
                $builder = ($operator == '%like%') ? static::likeAny($builder,$column_name) : $builder->where($column_name,$operator,head($query_params));
            else $builder = $builder->whereIn($column_name,$query_params);
        });

        return $builder->orderBy($search['order_by'],$search['dir']);
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isAcceptedValue($value)
    {
        $checked_value = trim($value);
        return (is_numeric($checked_value) or is_bool($value)) or ! empty($checked_value);
    }

    /**
     * @param Builder $builder
     * @param string|null $date_column
     * @param string $date_query_key
     * @return Builder
     */
    public static function applyDateFilter(Builder $builder, string $date_column = null, string $date_query_key = 'date') : Builder
    {
        if (! empty(request()->query($date_query_key))):
            $date_column ??= static::timestamp();
            $date_array = explode(',' ,request()->query($date_query_key));
            return count($date_array) == 1
                              ? $builder->whereDate(static::fullColumnName($date_column),db_date(head($date_array)))
                             : $builder->whereBetween(static::fullColumnName($date_column),[db_date(head($date_array),'startOfDay'),db_date(last($date_array),'endOfDay')]);
        else: return $builder;
        endif;
    }

    /**
     * @param Builder $builder
     * @return mixed
     */
    public static function accToTrash (Builder $builder) : Builder
    {
        $trash = (string)request()->query('trash');
        $acc_to_trash = optional(['included' => 'withTrashed', 'excluded' => 'withoutTrashed', 'only' => 'onlyTrashed']);
        return $builder->{$acc_to_trash[$trash] ?? $acc_to_trash['excluded']}();
    }

    /**
     * @param Builder $builder
     * @param string $column_name
     * @param string|null $query_key
     * @return Builder
     */
    public static function likeAny (Builder $builder, string $column_name, string $query_key = null) : Builder
    {
        $query = request()->query($query_key ?? $column_name);
        return isset($query) ? $builder->where($column_name,'like','%'.$query.'%') : $builder;
    }

    /**
     * @param Builder $builder
     * @param string $column
     * @param string $operator
     * @param string|null $query_key
     * @return Builder
     */
    public static function flagFilter(Builder $builder, string $column, string $operator = '=', string $query_key = null) : Builder
    {
        $query_array = explode(',' ,request()->query($query_key ?? $column));
        return count($query_array) == 1  ?  $builder->where(static::fullColumnName($column),$operator,head($query_array)) : $builder->whereIn(static::fullColumnName($column),$query_array);
    }

    /**
     * @param Builder $builder
     * @param string $column
     * @param string|null $query_key
     * @return Builder
     */
    public static function actualOrRangeFilter(Builder $builder, string $column, string $query_key = null) : Builder
    {
        $query_array = explode(',' ,request()->query($query_key ?? $column));
        return count($query_array) == 1  ?  $builder->where(static::fullColumnName($column),head($query_array))
            : $builder->whereBetween(static::fullColumnName($column),[head($query_array),last($query_array)]);
    }

    /**
     * @param Builder $builder
     * @param string $query_key
     * @param string $method
     * @param array $method_args
     * @return Builder
     */
    public static function applyOrSkip(Builder $builder, string $query_key, string $method, array $method_args = []) : Builder
    {
        return ! is_null(request()->query($query_key)) ? static::{$method}(...$method_args) : $builder;
    }

    /**
     * probable column that will be used in order by including main_table timestamps
     * @param array $main_table_columns
     * @param array $probable
     * @param string $query_key
     * @return string
     */
    public static function orderByProbability(array $main_table_columns, array $probable = [], string $query_key = 'order_by') : string
    {
        $order_by = request()->query($query_key);
        if (!empty($order_by)){
            $prepared = static::prepareOrderBy($order_by,$main_table_columns);
            return in_array($prepared['order_by'],[...$prepared['main_table_columns'],...$probable]) ? $prepared['order_by'] : static::createdAt();
        }
        return static::createdAt();
    }

    /**
     * @param string $order_by
     * @param array $main_table_columns
     * @return array
     */
    private static function prepareOrderBy(string $order_by, array $main_table_columns) : array
    {
        $table_columns = array_merge(array_combine($main_table_columns,static::prepareMainTableColumns($main_table_columns)),static::timestamps());
        $column = array_search($order_by,$table_columns);
        $column and $order_by = $table_columns[$column];
        in_array($order_by,array_keys($table_columns)) and $order_by = $table_columns[$order_by];
        return ['main_table_columns'=> array_values($table_columns),'order_by'=> $order_by];
    }

    private static function prepareMainTableColumns(array $main_table_columns) : array
    {
        return array_map(fn($column) => static::fullColumnName($column),$main_table_columns);
    }

    /**
     * declared timestamps keyed by generic others used as keys & values in preparing orderBy column to avoid ambiguous SQL error
     * @return string[]
     */
    private static function timestamps() : array
    {
        return ['created_at' => static::createdAt(),'updated_at'=> static::updatedAt(),'deleted_at'=> static::deletedAt()];
    }


    /**
     * @param string $created_at
     * @return string
     */
    public static function createdAt(string $created_at = 'created_at') : string
    {
        return static::fullColumnName($created_at);
    }

    /**
     * @param string $updated_at
     * @return string
     */
    public static function updatedAt(string $updated_at = 'updated_at') : string
    {
        return static::fullColumnName($updated_at);
    }

    /**
     * @param string $deleted_at
     * @return string
     */
    public static function deletedAt(string $deleted_at = 'deleted_at') : string
    {
        return static::fullColumnName($deleted_at);
    }

    /**
     * order by direction
     * @return string
     */
    private static function orderByDir() : string
    {
        return request()->query('dir') == 'asc' ? 'asc' : 'desc';
    }

    /**
     * @param string $column_name
     * @return string
     */
    public static function fullColumnName(string $column_name) : string
    {
        return static::mainTable().'.'.$column_name;
    }

    /**
     * @param string $timestamp_query_key
     * @return string
     */
    private static function timestamp(string $timestamp_query_key = 'timestamp') : string
    {
        $time_stamp = request()->query($timestamp_query_key);
        return in_array($time_stamp,['created_at','updated_at','deleted_at']) ? $time_stamp : 'created_at';
    }
}
