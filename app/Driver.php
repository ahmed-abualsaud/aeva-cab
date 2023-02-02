<?php /** @noinspection PhpUnnecessaryLocalVariableInspection */

namespace App;

use Aeva\Cab\Domain\Models\Trace;
use App\Settings;
use App\PartnerDriver;
use App\Traits\Filterable;
use App\Traits\Query;
use App\Traits\Searchable;

use App\Notifications\ResetPassword as ResetPasswordNotification;

use Aeva\Cab\Domain\Models\CabRequest;
use Aeva\Cab\Domain\Models\CabRequestTransaction;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class Driver extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use Searchable;
    use Filterable;
    use SoftDeletes;
    use Query;


    protected $connection ='mysql';
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
            'status' => '=',
            'cab_status' => '=',
            'referrer_id'=> '=',
            'title'=> '=',
            'approved'=> '=',
        ];
    }

    public static function mainTable(): string
    {
        return 'drivers';
    }

    public static function builder(): Builder
    {
        return self::query();
    }

    protected $guarded = [];

    protected $hidden = ['password'];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @param  string  $type
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, "drivers"));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function trips()
    {
        return $this->hasMany(BusinessTrip::class);
    }

    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'partner_drivers')
            ->select('id', 'name', 'logo');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicles')
                    ->withPivot('active');
    }

    public function car_type()
    {
        return $this->belongsTo(CarType::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function driverTransactions()
    {
        return $this->hasMany(DriverTransaction::class);
    }

    public function stats()
    {
        return $this->hasOne(DriverStats::class, 'driver_id');
    }

    public function logs()
    {
        return $this->hasMany(DriverLog::class, 'driver_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Admin::class, 'supplier_id');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }

    public function getSupplierNameAttribute()
    {
        return $this->supplier? $this->supplier->full_name: null;
    }

    public function scopeFleet($query, $args)
    {
        if (array_key_exists('fleet_id', $args) && $args['fleet_id']) {
            $query->where('fleet_id', $args['fleet_id']);
        }
        return $query;
    }

    public function scopeSupplier($query, $args)
    {
        if (array_key_exists('supplier_id', $args) && !$args['supplier_id'] || 
            array_key_exists('supplier_name', $args) && !$args['supplier_name']) {

                return $query->where('supplier_id', null);
        }

        if (array_key_exists('supplier_id', $args) && $args['supplier_id']) {
            $supplier_id = $args['supplier_id'];
        }

        if (array_key_exists('supplier_name', $args) && $args['supplier_name']) {
            $supplier = Admin::select('id')->where('full_name', $args['supplier_name'])->first();
            $supplier_id = $supplier ? $supplier->id : -1;
        }

        if(!empty($supplier_id))
            return $query->where('supplier_id', $supplier_id);

        return $query;
    }

    public function scopeAssigned($query, $args)
    {
        return $query->whereIn('id', PartnerDriver::byPartner($args))->latest();
    }

    public function scopeNotAssigned($query, $args)
    {
        return $query->whereNotIn('id', PartnerDriver::byPartner($args));
    }

    public function scopeSearch($query, $args)
    {
        if (array_key_exists('searchQuery', $args) && !empty_graph_ql_value($args['searchQuery'])) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }
        return $query;
    }

    public function scopeCabStatus($query, $args)
    {
        if (array_key_exists('cabStatus', $args) && !empty_graph_ql_value($args['cabStatus'])) {
            $status_array = explode(',',$args['cabStatus']);
            $query = count($status_array) == 1 ? $query->where('cab_status', head($status_array)) : $query->whereIn('cab_status',$status_array);
        }
        return $query;
    }

    public function scopeActiveStatus($query, $args)
    {
        if (array_key_exists('active_status', $args) && !empty_graph_ql_value($args['active_status'])) {
            Driver::whereRaw('suspension_till < ?', [date('Y-m-d H:i:s')])
                ->update([
                    'active_status' => 'Active',
                    'suspended_at' => null,
                    'suspension_till' => null,
                    'suspension_reason' => null
                ]);

            $query = $query->where('active_status', $args['active_status']);
        }
        return $query;
    }

    public function scopeTitle($query, $args)
    {
        if (array_key_exists('title', $args) && !empty_graph_ql_value($args['title'])) {
            $query = $query->where('title', $args['title']);
        }
        return $query;
    }

    public function scopeNearby($query, $args)
    {
        if (isset($args['lng'],$args['lat'])):
        $radius = Settings::where('name', 'Search Radius')->first()->value;

        $query = $query->selectRaw('id,
            full_name, phone, avatar, latitude, longitude,
            ST_Distance_Sphere(point(longitude, latitude), point(?, ?))
            as distance
            ', [$args['lng'], $args['lat']]
            )
            ->having('distance', '<=', $radius)
            ->where('cab_status', 'Online')
            ->groupBy('id')
            ->orderBy('distance','asc')
            ->take(10);
        endif;
        return $query;
    }

    public function scopeGetLatest($query, $args)
    {
        return $query->latest();
    }

    public static function updateLocation(string $lat, string $lng)
    {
        try {
            auth('driver')
                ->userOrFail()
                ->update(['latitude' => $lat, 'longitude' => $lng]);
        } catch (UserNotDefinedException $e) {
            //
        }
    }

    public function scopeApproved($query,$args)
    {
        if (array_key_exists('approved', $args) && !empty_graph_ql_value($args['approved'])){
            $query = static::applyBooleanFilter($query,$args['approved'],self::getTable().'.approved');
        }
        return $query;
    }

    public function scopeStatsTotalWorkingHours($query,$args)
    {
        if (array_key_exists('stats__total_working_hours',$args) && !empty_graph_ql_value($args['stats__total_working_hours']))
        {
            $query = $query->whereHas('stats',fn($stats) => $stats->when(
                array_key_exists('stats__created_at',$args) && !empty_graph_ql_value($args['stats__created_at']),
                fn($stats) => count($date_array = explode(',',$args['stats__created_at'])) == 1
                    ? $stats->whereDate('driver_stats.created_at',db_date(head($date_array)))
                    : $stats->whereBetween('driver_stats.created_at',[db_date(head($date_array)),db_date(last($date_array))])
            )->where('driver_stats.total_working_hours','>=',$args['stats__total_working_hours']));
        }
        return $query;
    }

    public function scopeLogsTotalWorkingHours($query,$args)
    {
        if (array_key_exists('logs__total_working_hours',$args) && !empty_graph_ql_value($args['logs__total_working_hours']))
        {
            /*
            $query = $query->withSum(['logs as logs__total_working_hours'=> fn($logs) => $logs->when(
                array_key_exists('logs__created_at',$args) && !empty_graph_ql_value($args['logs__created_at']),
                fn($logs) => count($date_array = explode(',',$args['logs__created_at'])) == 1
                    ? $logs->whereDate('driver_logs.created_at',db_date(head($date_array)))
                    : $logs->whereBetween('driver_logs.created_at',[db_date(head($date_array)),db_date(last($date_array))])
            )],'total_working_hours')->having('logs__total_working_hours','>=',$args['logs__total_working_hours']);
            */

            $query = $query->withAggregate(['logs as logs__total_working_hours'=> fn($logs) => $logs->when(
                array_key_exists('logs__created_at',$args) && !empty_graph_ql_value($args['logs__created_at']),
                fn($logs) => count($date_array = explode(',',$args['logs__created_at'])) == 1
                    ? $logs->whereDate('driver_logs.created_at',db_date(head($date_array)))
                    : $logs->whereBetween('driver_logs.created_at',[db_date(head($date_array)),db_date(last($date_array))])
            )],'sum(total_working_time/60)')->having('logs__total_working_hours','>=',$args['logs__total_working_hours']);
        }
        return $query;
    }

    public function scopeSearchApplied($query)
    {
        $args = request()->query();
        $optional = optional($args);

        self::scopeSearch($query,$args);
        self::scopeFleet($query,$args);
        self::scopeApproved($query,$args);
        self::scopeCabStatus($query,$args);
        self::scopeTitle($query,$args);
        self::scopeNearby($query,$args);
        self::scopeStatsTotalWorkingHours($query,$args);
        self::scopeLogsTotalWorkingHours($query,$args);
        self::scopeActiveStatus($query,$args);

        !empty_graph_ql_value($optional['created_at']) and $query = self::dateFilter($optional['created_at'],$query,self::getTable().'.created_at');
        !empty_graph_ql_value($optional['updated_at']) and $query = self::dateFilter($optional['updated_at'],$query,self::getTable().'.updated_at');
        return self::scopeGetLatest($query,$args);
    }

    public function getLastLogAttribute()
    {
        $last_log = $this->logs->last();
        if ($last_log && substr($last_log->created_at, 0, 10) == date('Y-m-d')) {
            return $last_log;
        }
        return null;
    }

    public function traces()
    {
        return $this->hasMany(Trace::class,'guard_id','id')->where('guard','=','driver');
    }
}
