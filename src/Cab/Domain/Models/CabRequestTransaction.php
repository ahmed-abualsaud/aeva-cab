<?php /** @noinspection DuplicatedCode */

namespace Aeva\Cab\Domain\Models;

use App\User;
use App\Driver;

use App\Traits\Filterable;
use App\Traits\Searchable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabRequestTransaction extends Model
{
    use Filterable;
    use Searchable;
    use SoftDeletes;

    protected $guarded = [];
    protected $connection ='mysql';

    public function user()
    {
        return $this->setConnection('mysql2')->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->setConnection('mysql')->belongsTo(Driver::class);
    }

    public function request()
    {
        return $this->setConnection('mysql')->belongsTo(CabRequest::class);
    }

    public function scopeSearch($query, $args)
    {

        if (array_key_exists('searchQuery', $args) && !empty_graph_ql_value($args['searchQuery'])) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }
    }

    public function scopeFilter($query, $args)
    {
        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }
    }

    public function scopeDriver($query, $args)
    {
        if (array_key_exists('driver_id', $args) && $args['driver_id']) {
            return $query->where('driver_id', $args['driver_id']);
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
        self::scopeFilter($query,$args);

        !empty_graph_ql_value($optional['created_at']) and $query = self::dateFilter($optional['created_at'],$query,self::getTable().'.created_at');
        !empty_graph_ql_value($optional['updated_at']) and $query = self::dateFilter($optional['updated_at'],$query,self::getTable().'.updated_at');

        return self::scopeGetLatest($query,$args)->with('user','driver');
    }
}
