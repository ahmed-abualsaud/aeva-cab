<?php

namespace App;

use App\Traits\Filterable;
use App\Traits\Query;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DriverTransaction extends Model
{
    use Searchable, Filterable,Query;
    /*
    public static string $main_table;
    public static array $filters;
    public static array $search;
    public static Builder $builder;
*/

    public static function filters(): array
    {
        return [
            'id'=> '=',
            'driver_id' => '=',
            'admin_type' => 'like',
            'amount'=> 'like',
            'notes'=> '=',
            'approved'=> '=',
        ];
    }

    public static function mainTable(): string
    {
        return 'driver_transactions';
    }

    public static function builder(): Builder
    {
        return self::query();
    }


    protected $guarded = [];

	public $timestamps = false;

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function scopeSearch($query, $args)
    {
        if (array_key_exists('searchQuery', $args) && !empty_graph_ql_value($args['searchQuery'])) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }
        return $query;
    }

    public function scopePeriod($query, $args)
    {
        if (array_key_exists('period', $args) && !empty_graph_ql_value($args['period'])) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->latest();
    }

    public function scopeType($query, $args)
    {
        if (array_key_exists('type', $args) && !empty_graph_ql_value($args['type'])) {
            return $query->where('type', $args['type']);
        }

        return $query;
    }

    public function scopeGetLatest($query, $args)
    {
        return $query->latest();
    }

    public function scopeSearchApplied($query)
    {
        $args = request()->query();
        $optional = optional($args);

        self::scopeSearch($query,$args);
        self::scopePeriod($query,$args);
        self::scopeType($query,$args);

        !empty_graph_ql_value($optional['created_at']) and $query = self::dateFilter($optional['created_at'],$query,self::getTable().'.created_at');
        !empty_graph_ql_value($optional['updated_at']) and $query = self::dateFilter($optional['updated_at'],$query,self::getTable().'.updated_at');
        return self::scopeGetLatest($query,$args);
    }
}
