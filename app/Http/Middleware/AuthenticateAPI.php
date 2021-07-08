<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/07/04
 * Time: 11:20
 */

namespace App\Http\Middleware;


use App\Services\AuthService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthenticateAPI
{
    /**
     * @var AuthService
     */
    private $service;

    /**
     * AuthenticateAPI constructor.
     * @param AuthService $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        abort_if(!$request->hasCookie('access_token') || !$request->hasCookie('csrf_token'), Response::HTTP_UNAUTHORIZED);

        if (!$this->service->isAuthenticated($request->cookie('access_token'))) {
            if ($refreshToken = $request->cookie('refresh_token')) {
                // Retry to login
                try {
                    $userTokenData = $this->service->retryLogin($refreshToken);

                    $response = $next($request);
                    $response->withCookie('access_token', $userTokenData['access_token'])
                        ->withCookie('refresh_token', $userTokenData['refresh_token'])
                        ->withCookie('csrf_token', $userTokenData['csrf_token']);

                    return $response;
                } catch (ClientException $exception) {
                    // do nothing
                }
            }
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        return $next($request);
    }
}