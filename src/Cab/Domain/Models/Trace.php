<?php
/** @noinspection SpellCheckingInspection */
/** @noinspection PhpMissingReturnTypeInspection */

namespace Aeva\Cab\Domain\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Driver;

class Trace extends Model
{
    use SoftDeletes,Searchable,Filterable;

    /**
     * @var string
     */
    protected $table = 'traces';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $attributes = [
      'guard'=> 'driver'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['guard','guard_id','event','latitude','longitude'];


    /**
     * @param $guard
     * @return string
     */
    public function getGuradAttribute($guard)
    {
        return strtoupper($guard);
    }

    /**
     * @param $guard
     * @return string
     */
    public function setGuradAttribute($guard)
    {
        return strtolower($guard);
    }

    /**
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $query
     * @param $args
     * @return mixed
     */
    public function scopeSearch($query, $args)
    {
        if (array_key_exists('searchQuery', $args) && !empty_graph_ql_value($args['searchQuery'])) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }
        return $query;
    }

    public function scopefilter($query, $args)
    {
        if (array_key_exists('period', $args) && !empty_graph_ql_value($args['period'])) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        if (array_key_exists('created_at', $args) && !empty_graph_ql_value($args['created_at'])) {
            $query = $this->dateFilter($args['created_at'], $query, 'created_at');
        }

        if (array_key_exists('guard_id', $args) && !empty_graph_ql_value($args['guard_id'])) {
            $query = $query->where('guard_id', $args['guard_id']);
        }

        if (array_key_exists('guard', $args) && !empty_graph_ql_value($args['guard'])) {
            $query = $query->where('guard', strtolower($args['guard']));
        }

        return $query;
    }

    public function scopeGetLatest($query,$args)
    {
        return $query->latest();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeSearchApplied($query)
    {
        $args = request()->query();
        self::scopeSearch($query,$args);
        self::scopefilter($query,$args);
        return self::scopeGetLatest($query,$args);
    }


    public function driver()
    {
        return $this->belongsTo(Driver::class,'guard_id','id','traces');
    }
}
