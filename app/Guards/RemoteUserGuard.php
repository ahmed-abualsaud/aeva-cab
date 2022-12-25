<?php
namespace App\Guards;

use App\User;
use Tymon\JWTAuth\JWT;

use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;

class RemoteUserGuard implements Guard
{
    use GuardHelpers;
    /**
     * @var JWT $jwt
     */
    protected JWT $jwt;
    /**
     * @var Request $request
     */
    protected Request $request;
    /**
     * RemoteUserGuard constructor.
     * @param JWT $jwt
     * @param Request $request
     */
    public function __construct(JWT $jwt, Request $request) 
    {
        $this->jwt = $jwt;
        $this->request = $request;
    }

    public function user() 
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if ($this->jwt->setRequest($this->request)->getToken() && $this->jwt->check()) 
        {
            $id = $this->jwt->payload()->get('sub');
            $this->user = new User();
            $this->user->id = $id;
            return $this->user;
        }

        return null;
    }

    public function setToken($token) 
    {
        $this->jwt->setToken($token);
        $id = $this->jwt->payload()->get('sub');
        $this->user = User::find($id);
        return $this;
    }

    public function onceUsingId($user_id)
    {
        return $this->jwt->onceUsingId($user_id);
    }

    public function fromUser($user)
    {
        return $this->jwt->fromUser($user);
    }

    public function validate(array $credentials = []) 
    {
    }

    public function isBlocked()
    {
        try {
            $user = User::findOrFail($this->jwt->payload()->get('sub'));
            if (!$user->is_active) {
                return true;
            }
        } catch (\Exception $e) {}

        return false;
    }
}