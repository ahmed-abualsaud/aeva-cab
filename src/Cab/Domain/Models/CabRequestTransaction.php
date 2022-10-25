<?php /** @noinspection DuplicatedCode */

namespace Aeva\Cab\Domain\Models;

use App\User;
use App\Driver;

use App\Traits\Filterable;
use App\Traits\Searchable;

use App\Exceptions\CustomException;

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

    public static function create($data)
    {
        if (!in_array($data['payment_method'], ['Cashout', 'Scan And Pay']) &&
            parent::where('payment_method', $data['payment_method'])->where('request_id', $data['request_id'])->exists())
        {
            throw new CustomException(__('lang.trx_exists'));
        }

        if (in_array($data['payment_method'], ['Cashout', 'Scan And Pay']) &&
            parent::where('payment_method', $data['payment_method'])->where('reference_number', $data['reference_number'])->exists())
        {
            throw new CustomException(__('lang.trx_exists'));
        }

        return static::query()->create($data);
    }

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
        $query = self::scopePeriodFilter($query,$args);
        $query = self::scopeDriver($query,$args);
        if (array_key_exists('user_id', $args) && !empty_graph_ql_value($args['user_id'])) {
            $query = $query->where('user_id','=',$args['user_id']);
        }
        return $query;
    }

    public function scopePeriodFilter($query, $args)
    {
        if (array_key_exists('period', $args) && !empty_graph_ql_value($args['period'])) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }
        return $query;
    }

    public function scopeCashoutExcluded($query, $args)
    {
        $query = $query->where('payment_method','!=','Cashout');
        return $query;
    }

    public function scopeDriver($query, $args)
    {
        if (array_key_exists('driver_id', $args) && !empty_graph_ql_value($args['driver_id'])) {
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

        self::scopeCashoutExcluded($query,$args);
        self::scopeSearch($query,$args);
        self::scopeFilter($query,$args);

        !empty_graph_ql_value($optional['created_at']) and $query = self::dateFilter($optional['created_at'],$query,self::getTable().'.created_at');
        !empty_graph_ql_value($optional['updated_at']) and $query = self::dateFilter($optional['updated_at'],$query,self::getTable().'.updated_at');

        return self::scopeGetLatest($query,$args)->with('user','driver');
    }

    public function scopeCashOut($query)
    {
        $args = request()->query();
        $optional = optional($args);

        $query = $query->where('payment_method','=','Cashout');
        self::scopeSearch($query,$args);
        self::scopeDriver($query,$args);
        self::scopePeriodFilter($query,$args);

        !empty_graph_ql_value($optional['created_at']) and $query = self::dateFilter($optional['created_at'],$query,self::getTable().'.created_at');
        !empty_graph_ql_value($optional['updated_at']) and $query = self::dateFilter($optional['updated_at'],$query,self::getTable().'.updated_at');

        return self::scopeGetLatest($query,$args)->with('driver');
    }

    public function scopeScanAndPay($query)
    {
        $args = request()->query();
        $optional = optional($args);

        $query = $query->where('payment_method','=','Scan And Pay');
        self::scopeSearch($query,$args);
        self::scopeDriver($query,$args);
        self::scopePeriodFilter($query,$args);

        !empty_graph_ql_value($optional['created_at']) and $query = self::dateFilter($optional['created_at'],$query,self::getTable().'.created_at');
        !empty_graph_ql_value($optional['updated_at']) and $query = self::dateFilter($optional['updated_at'],$query,self::getTable().'.updated_at');

        return self::scopeGetLatest($query,$args)->with('driver');
    }
}
