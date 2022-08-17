<?php /** @noinspection DuplicatedCode */

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Jobs;

use Aeva\Cab\Domain\Traits\CabRequestHelper;
use App\Driver;
use App\DriverStats;
use App\Traits\BulkQuery\BulkQuery;
use App\Settings;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ChangeDriverStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,CabRequestHelper;
    protected string $duration_key = 'Acceptable Online Duration';
    protected string $activation_key = 'Activated Driver Status Cron Job';

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $settings = Settings::query()->whereIn('name',[$this->duration_key,$this->activation_key])->pluck('value','name');
        $now = Carbon::now();
        $acceptable_time = $now->subMinutes($settings[$this->duration_key])->format('Y-m-d H:i:s');
        $settings[$this->activation_key] == 'on' and $this->accToLocationUpdatedAt($now,$acceptable_time);
    }

    /**
     * @param Carbon $now
     * @param $acceptable_time
     * @return void
     */
    public function accToActivityUpdatedAt(Carbon $now,$acceptable_time)
    {
        try {
            DB::beginTransaction();
            $fake_online_drivers = Driver::query()->where('cab_status','=','Online')->with('stats')
                ->whereHas('stats',fn($query) => $query->where('activity_updated_at','<=',$acceptable_time));

            $fake_online_drivers->cursor()->pluck('stats')->map(function($stat) use ($now){
                $stat['updates'] = [
                    'total_working_time'=> $stat['total_working_time'] + $now->diffInMinutes(Carbon::parse($stat['activity_updated_at'])),
                    'activity_updated_at'=> $now,
                ];
                return $stat;
            })->chunk(500)->each(fn($chunked) => BulkQuery::update('driver_stats',['total_working_time','activity_updated_at'],$chunked->pluck('updates','id')->all()));

            $fake_online_drivers->update(['cab_status'=> 'Offline']);

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
        }
    }

    /**
     * @param Carbon $now
     * @param $acceptable_time
     * @return void
     */
    public function accToLocationUpdatedAt(Carbon $now,$acceptable_time)
    {
        try {
            DB::beginTransaction();
            $fake_online_drivers = Driver::query()->where('cab_status','=','Online')->with('stats')
                ->where('location_updated_at','<=',$acceptable_time);

            $fake_online_drivers->cursor()->pluck('stats')->map(function($stat) use ($now){
                $stat['updates'] = [
                    'total_working_time'=> $stat['total_working_time'] + $now->diffInMinutes(Carbon::parse($stat['activity_updated_at'])),
                    'activity_updated_at'=> $now,
                ];
                return $stat;
            })->chunk(500)->each(fn($chunked) => BulkQuery::update('driver_stats',['total_working_time','activity_updated_at'],$chunked->pluck('updates','id')->all()));

            $fake_online_drivers->update(['cab_status'=> 'Online']);

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
        }
    }
}
