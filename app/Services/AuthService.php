<?php

namespace App\Services;


use App\Exceptions\NotFound;
use App\Exceptions\OperationNotPermitted;
use App\Http\Controllers\Helpers\RequestHelper;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Redis;

/**
 * Class AuthService
 * @package App\Services
 */
class AuthService
{
    /**
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * @var Redis
     */
    private $redis;

    /**
     * AuthService constructor.
     * @param RequestHelper $requestHelper
     * @param Redis $redis
     */
    public function __construct(RequestHelper $requestHelper, Redis $redis)
    {
        $this->requestHelper = $requestHelper;
        $this->redis = $redis;
    }

    /**
     * @param string $accessToken
     * @return bool
     */
    public function isAuthenticated(string $accessToken): bool
    {
        $userTokenData = json_decode($this->redis->get($accessToken));

        if (!$userTokenData) {
            return false;
        }

        $tokenExpiresAt = $userTokenData->expires_at;

        return strtotime($tokenExpiresAt) > time();
    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     * @throws NotFound
     * @throws OperationNotPermitted
     * @throws \Exception
     */
    public function authenticate(string $email, string $password)
    {
        try {
            $userTokenData = $this->requestHelper->call('users/login', 'POST', [
                'user_name' => $email,
                'password' => $password
            ]);
        } catch (ClientException $exception) {
            if ($exception->getCode() == Response::HTTP_NOT_FOUND) {
                throw new NotFound('Incorrect email or password');
            } elseif ($exception->getCode() == Response::HTTP_FORBIDDEN) {
                throw new OperationNotPermitted('Access denied');
            } else {
                throw new \Exception('Operation failed', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $userTokenData = $userTokenData['message']['data'];

        $this->redis->set(
            $userTokenData['access_token'],
            json_encode($userTokenData),
            strtotime($userTokenData['expires_at']) - time()
        );

        return new User($userTokenData);
    }

    /**
     * De-Authenticate user from Redis
     */
    public function deAuthenticate()
    {
        $this->redis->del(
            request()->cookie('access_token')
        );
    }

    /**
     * @param string $refreshToken
     * @return mixed
     */
    public function retryLogin(string $refreshToken)
    {
        $userTokenData = $this->requestHelper->call(
            'users/refreshToken',
            'POST',
            [
                'refresh_token' => $refreshToken
            ]
        );

        $this->redis->set(
            $userTokenData['message']['data']['access_token'],
            json_encode($userTokenData['message']['data']),
            strtotime($userTokenData['message']['data']['expires_at']) - time()
        );

        return $userTokenData['message']['data'];
    }
}
