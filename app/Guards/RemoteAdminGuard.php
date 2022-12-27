<?php
namespace App\Guards;

use App\Admin;
use Tymon\JWTAuth\JWT;

use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;

class RemoteAdminGuard implements Guard
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
     * RemoteAdminGuard constructor.
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

        return null;
    }

    public function attempt($credentials) 
    {
        $id = $this->jwt->payload()->get('sub');
        $user = Admin::find($id);
        //$user = $this->provider->retrieveByCredentials($credentials);
        $token = $this->jwt->fromUser($user);
        $this->jwt->setToken($token);
        //$this->setUser($user);

        return $token;
    }

    public function setToken($token) 
    {
        $this->jwt->setToken($token);
        $id = $this->jwt->payload()->get('sub');
        $this->user = Admin::find($id);
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
}