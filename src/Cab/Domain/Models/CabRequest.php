<?php /** @noinspection DuplicatedCode */

namespace Aeva\Cab\Domain\Models;

use App\User;
use App\Driver;
use App\Vehicle;
use App\PromoCode;

use App\Traits\Query;
use App\Traits\Filterable;
use App\Traits\Searchable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabRequest extends Model
{
    use Filterable;
    use Searchable;
    use SoftDeletes;
    use Query;
    protected $connection = 'mysql';

    public static function filters(): array
    {
        return [
            'id'=> '=',
            'user_id' => '=',
            'driver_id' => '=',
            'vehicle_id'=> '=',
            'promo_code_id'=> '=',
            'history'=> '%like%',
            'route_key'=> '%like%',
            'map_url'=> '%like%',
            'schedule_time'=> '=',
            'next_free_time'=> '=',
            'paid'=> '=',
            'rated'=> '=',
            'costs'=> '=',
            's_address'=> '%like%',
            's_lat'=> '=',
            's_lng'=> '=',
            'd_address'=> '=',
            'd_lat'=> '=',
            'd_lng'=> '=',
            'notes'=> '%like%',
        ];
    }

    public static function mainTable(): string
    {
        return 'cab_requests';
    }

    public static function builder(): Builder
    {
        return self::query();
    }


    protected $guarded = [];

    protected $appends = ['costs_after_discount', 'discount'];

    protected $casts = [
        'history' => 'json',
    ];

    public function user()
    {
        return $this->setConnection('mysql2')->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->setConnection('mysql')->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->setConnection('mysql')->belongsTo(Vehicle::class);
    }

    public function rating()
    {
        return $this->setConnection('mysql')->hasOne(CabRating::class, 'request_id');
    }

    public function transactions()
    {
        return $this->setConnection('mysql')->hasMany(CabRequestTransaction::class, 'request_id');
    }

    public function traces()
    {
        return $this->setConnection('mysql')->hasMany(Trace::class, 'request_id');
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

    public function scopeUserLive($query, $args)
    {
        if (array_key_exists('user_id', $args) && !empty_graph_ql_value($args['user_id'])) {
            return $query->where('user_id', $args['user_id'])
                ->whereNotIn('status' , ['Scheduled', 'Cancelled', 'Completed', 'Ended'])
                ->orWhere(function ($query) {
                    $query->where('status', 'Completed')
                            ->where('rated', false);
                });
        }
        return $query;
    }

    public function scopeDriverLive($query, $args)
    {
        if (array_key_exists('driver_id', $args) && !empty_graph_ql_value($args['driver_id'])) {
            return $query->where('driver_id', $args['driver_id'])
                ->whereNotIn('status' , ['Scheduled', 'Cancelled', 'Completed']);
        }
        return $query;
    }

    public function scopeWherePending($query, $user_id)
    {
        return $query->where('user_id', $user_id)
            ->whereNotIn('status' , ['Scheduled', 'Cancelled', 'Completed', 'Ended']);
    }

    public function scopeWhereScheduled($query, $user_id)
    {
        return $query->where('user_id', $user_id)
            ->where('status', 'Scheduled');
    }

    public function scopeSearch($query, $args)
    {

        if (array_key_exists('searchQuery', $args) && !empty_graph_ql_value($args['searchQuery'])) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }
        return $query;
    }

    public function scopeFilter($query, $args)
    {
        if (array_key_exists('driver_id', $args) && !empty_graph_ql_value($args['driver_id'])) {
            $query = $query->where('driver_id', $args['driver_id']);
        }

        if (array_key_exists('period', $args) && !empty_graph_ql_value($args['period'])) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        if (array_key_exists('period', $args) && !empty_graph_ql_value($args['period'])) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        if (array_key_exists('user_id', $args) && !empty_graph_ql_value($args['user_id'])) {
            $query = $query->where('user_id', $args['user_id']);
        }

        if (array_key_exists('status', $args) && !empty_graph_ql_value($args['status'])) {
            $query = $query->where('status', $args['status']);
        }

        if (array_key_exists('paid', $args) && !empty_graph_ql_value($args['paid'])) {
            $query = static::applyBooleanFilter($query,$args['paid'],self::getTable().'.paid');
        }

        if (array_key_exists('rated', $args) && !empty_graph_ql_value($args['rated'])) {
            $query = static::applyBooleanFilter($query,$args['rated'],self::getTable().'.rated');
        }

        if (array_key_exists('user_id', $args) && !empty_graph_ql_value($args['user_id'])) {
            $query = $query->where('user_id','=',$args['user_id']);
        }

        return $query;
    }

    public function scopePending($query, $args)
    {
        return $query->where($args['issuer_type'].'_id', $args['issuer_id'])
            ->whereNotIn('status' , ['Scheduled', 'Cancelled', 'Ended', 'Completed']);
    }

    public function scopeGetLatest($query, $args)
    {
        return $query->latest();
    }

    public function getCostsAfterDiscountAttribute()
    {
        $promoCode = $this->promoCode;

        if ( !($promoCode && $this->costs) ) {return $this->costs;}

        $promoCode = PromoCode::find($promoCode->id);
        $discount_rate = ($this->costs * $promoCode->percentage / 100);

        if ($discount_rate > $promoCode->max_discount) {
            $discount_rate = $promoCode->max_discount;
        }
        return ceil($this->costs - $discount_rate);
    }

    public function getDiscountAttribute()
    {
        $promoCode = $this->promoCode;

        if ( !($promoCode && $this->costs) ) {return 0;}

        $promoCode = PromoCode::find($promoCode->id);
        $discount_rate = ($this->costs * $promoCode->percentage / 100);

        if ($discount_rate > $promoCode->max_discount) {
            $discount_rate = $promoCode->max_discount;
        }
        return floor($discount_rate);
    }


    public function scopeSearchApplied($query)
    {
        $args = request()->query();
        $optional = optional($args);

        self::scopeSearch($query,$args);
        self::scopeFilter($query,$args);

        !empty_graph_ql_value($optional['created_at']) and $query = self::dateFilter($optional['created_at'],$query,self::getTable().'.created_at');
        !empty_graph_ql_value($optional['updated_at']) and $query = self::dateFilter($optional['updated_at'],$query,self::getTable().'.updated_at');

        return self::scopeGetLatest($query,$args);
    }

    public function getPaymentMethodAttribute()
    {
        $history = $this->history;
        if (array_key_exists('sending', $history) && $history['sending'] &&
            array_key_exists('payment_method', $history['sending']) && $history['sending']['payment_method']) {
            return $history['sending']['payment_method'];
        }

        return 'unknown';
    }
}
