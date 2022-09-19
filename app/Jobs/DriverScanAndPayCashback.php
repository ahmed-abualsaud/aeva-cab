<?php /** @noinspection DuplicatedCode */

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Jobs;

use Aeva\Cab\Domain\Traits\CabRequestHelper;
use App\Driver;
use App\DriverStats;
use App\DriverTransaction;
use App\Traits\BulkQuery\BulkQuery;
use App\Settings;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\DB;
use function React\Promise\all;

class DriverScanAndPayCashback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected static string $settings_primary_key = 'scan_to_pay_cashback_settings';
    protected static string $max_amount_key = 'Driver Scan And Pay Cashback Max Amount';
    protected static string $percent_key = 'Driver Scan And Pay Cashback Percent';
    protected static string $daily_at_key = 'Driver Scan And Pay Cashback Daily At';
    protected static string $transactions_per_queue_key = 'Driver Scan And Pay Cashback Transactions Per Queue';
    protected static string $activation_key = 'Activated Driver Scan And Pay Cashback Cron Job';

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $settings = static::settings();
        $settings[static::$activation_key] == 'on' and $this->scanAndPayCashback();

    }

    /**
     * @return Collection
     */
    public static function settings() : Collection
    {
        return Cache::get(static::$settings_primary_key) ?? static::cacheSettings();
    }

    /**
     * @return string
     */
    public static function dailyAt() : string
    {
        return static::settings()[static::$daily_at_key];
    }

    /**
     * @return LazyCollection
     */
    public function drivers() : LazyCollection
    {
        return Driver::query()->where('active_status','=','Active')->join('driver_stats','drivers.id','=','driver_stats.driver_id')
            ->select('drivers.id','drivers.device_id','driver_stats.wallet','driver_stats.id as wallet_id')->withSum([
            'driverTransactions as total_scan_and_pay_amount'=> fn(Builder $driver_transactions) => $driver_transactions->where('type','=','Scan And Pay')
                ->whereDate('driver_transactions.created_at',Carbon::yesterday())
        ],'amount')->having('total_scan_and_pay_amount','>',zero())->cursor();
    }

    /**
     * @param LazyCollection $drivers
     * @param Collection $settings
     * @return LazyCollection
     */
    public function updates(LazyCollection $drivers, Collection $settings) : LazyCollection
    {
        $max_cashback = custom_number($settings[static::$max_amount_key]);
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return $drivers->map(fn($driver) => [
            'driver_id'=> $driver_id = $driver->id,
            'wallet_id'=> $driver->wallet_id,
            'device_id'=> $driver->device_id,
            'amount'=> $amount = custom_number( ($calculated_amount = $settings[static::$percent_key] * $driver['total_scan_and_pay_amount']) >= $max_cashback ? $max_cashback : $calculated_amount),
            'created_at'=> $created_at = $now,
            'updated_at'=> $updated_at = $now,
//            'case'=> "WHEN $driver->wallet_id THEN ?",
            'wallet'=> $wallet = custom_number($driver->wallet + $amount),
            'wallet_update'=> compact('wallet','updated_at'),
            'transaction'=> compact('driver_id','amount','created_at') + [
                'type'=> 'Scan And Pay Cashback',
                'notes'=> $settings[static::$percent_key] * 100 ."% as a cashback for " .$driver['total_scan_and_pay_amount']." L.E scan and pay"],
        ]);
    }

    /**
     * @param Collection|null $settings
     * @return Collection
     */
    public static function cacheSettings(Collection $settings = null) : Collection
    {
        $settings ??= Settings::query()->whereIn('name',[
            static::$max_amount_key,
            static::$percent_key,
            static::$daily_at_key,
            static::$transactions_per_queue_key,
            static::$activation_key
        ])->pluck('value','name');
        Cache::put(static::$settings_primary_key,$settings);
        return $settings;
    }

    /**
     * @return void
     */
    public function scanAndPayCashback()
    {
        $updates = @$this->updates($this->drivers(),$settings = static::settings());
        @$updates->isNotEmpty() and $updates->chunk($settings[static::$transactions_per_queue_key])->each(function($chunked){
            try {
                DB::beginTransaction();
                BulkQuery::update('driver_stats',['wallet','updated_at'],$chunked->pluck('wallet_update','wallet_id')->all());
                DriverTransaction::query()->insert($chunked->pluck('transaction')->all());
                DB::commit();
                SendPushNotification::dispatch(
                    $chunked->pluck('device_id')->all(),
                    __('lang.scanAndPayCashbackBody'),
                    __('lang.scanAndPayCashbackTitle'),
                );
            }catch (\Exception $e){
                DB::rollBack();
                response_error("drivers scan and pay cashback failed");
            }
        });
    }

}
