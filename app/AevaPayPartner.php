<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\SoftDeletes;
class AevaPayPartner extends Model
{
    use Searchable;
    use SoftDeletes;

    protected $connection = 'mysql2';
    protected $table = 'partners';
}
