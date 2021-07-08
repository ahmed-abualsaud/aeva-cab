<?php   

namespace App\Repository\Eloquent;   

use App\Repository\EloquentRepositoryInterface; 
use Illuminate\Database\Eloquent\Model;   

class BaseRepository implements EloquentRepositoryInterface 
{     
    /**      
     * @var Model      
     */     
     protected $model;       

    /**      
     * BaseRepository constructor.      
     *      
     * @param Model $model      
     */     
    public function __construct(Model $model)     
    {         
        $this->model = $model;
    }


    public function create(array $args)
    {
        //do nothing 
    }

    public function update(array $args)
    {
        //do nothing 
    }

    public function login(array $args)
    {
        //do nothing 
    }

    public function updatePassword(array $args)
    {
        //do nothing
    }

    public function destroy(array $args)
    {
        //do nothing
    }

    public function invoke(array $args)
    {
        //do nothing
    }

    public function updateRoute(array $args)
    {
        //do nothing
    }

    public function changeStatus(array $args)
    {
        //do nothing
    }
}