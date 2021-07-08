<?php

namespace App\Http\Middleware;


use App\Services\AuthService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class Authenticate
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Throwable
     */
    public function handle(Request $request, \Closure $next)
    {
        $isAuthenticated = $request->hasCookie('access_token') &&
            $this->authService->isAuthenticated($request->cookie('access_token'));

        if (!$isAuthenticated) {
            if ($refreshToken = $request->cookie('refresh_token')) {
                // Retry to login
                try {
                    $userTokenData = $this->authService->retryLogin($refreshToken);
                    $this->setCookies($request, $userTokenData);

                    return $next($request);
                } catch (ClientException $exception) {
                    // do nothing
                }
            }

            if ($referrer = $request->path()) {
                $redirect = sprintf('/login?redirect=%s', $referrer);
            } else {
                $redirect = '/login';
            }

            return redirect($redirect)
                ->withCookie(Cookie::forget('access_token'))
                ->withCookie(Cookie::forget('refresh_token'))
                ->withCookie(Cookie::forget('csrf_token'));
        }

        return $next($request);
    }

    /**
     * @param $request
     * @param array $userTokenData
     */
    private function setCookies($request, array $userTokenData)
    {
        $request->cookies->set('access_token',  $userTokenData['access_token']);
        $request->cookies->set('csrf_token', $userTokenData['csrf_token']);
        $request->cookies->set('refresh_token', $userTokenData['refresh_token']);
        Cookie::queue('access_token', $userTokenData['access_token']);
        Cookie::queue('refresh_token', $userTokenData['refresh_token']);
        Cookie::queue('csrf_token', $userTokenData['csrf_token']);
    }
}
