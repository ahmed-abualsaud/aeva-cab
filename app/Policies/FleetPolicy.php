<?php

namespace App\Policies;

use App\Fleet;
use App\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class FleetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any fleets.
     *
     * @param  \App\Role  $user
     * @return mixed
     */
    public function viewAny(Role $user)
    {
        //
    }

    /**
     * Determine whether the user can view the fleet.
     *
     * @param  \App\Role  $user
     * @param  \App\Fleet  $fleet
     * @return mixed
     */
    public function view(Role $user, Fleet $fleet)
    {
        //
    }

    /**
     * Determine whether the user can create fleets.
     *
     * @param  \App\Role  $user
     * @return mixed
     */
    public function create(Role $user)
    {
        return $user->fleet;
    }

    /**
     * Determine whether the user can update the fleet.
     *
     * @param  \App\Role  $user
     * @param  \App\Fleet  $fleet
     * @return mixed
     */
    public function update(Role $user, Fleet $fleet)
    {
        //
    }

    /**
     * Determine whether the user can delete the fleet.
     *
     * @param  \App\Role  $user
     * @param  \App\Fleet  $fleet
     * @return mixed
     */
    public function delete(Role $user, Fleet $fleet)
    {
        //
    }

    /**
     * Determine whether the user can restore the fleet.
     *
     * @param  \App\Role  $user
     * @param  \App\Fleet  $fleet
     * @return mixed
     */
    public function restore(Role $user, Fleet $fleet)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the fleet.
     *
     * @param  \App\Role  $user
     * @param  \App\Fleet  $fleet
     * @return mixed
     */
    public function forceDelete(Role $user, Fleet $fleet)
    {
        //
    }
}
