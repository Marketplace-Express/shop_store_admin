<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/07/02
 * Time: 14:16
 */

namespace App\Http\Middleware;


use App\Http\Controllers\Helpers\RequestHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPermissions
{
    /**
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * UserPermissions constructor.
     * @param RequestHelper $requestHelper
     * @param \Redis $redis
     */
    public function __construct(RequestHelper $requestHelper, \Redis $redis)
    {
        $this->requestHelper = $requestHelper;
        $this->redis = $redis;
    }

    public function handle(Request $request, \Closure $next)
    {
        $userPermissions = json_decode($this->redis->hGet(
            $request->get('user')->user_id,
            'permissions'
        ));

        if (empty($userPermissions)) {
            $userPermissions = $this->requestHelper->call(
                sprintf('users/%s/permissions', $request->get('user')->user_id),
                'GET',
                [],
                [
                    'Authorization' => sprintf('Bearer %s', $request->cookie('access_token')),
                    'csrf-token' => $request->cookie('csrf_token')
                ],
                [
                    'storeId' => 'e3e42b16-cb59-11eb-ab98-0242ac120003', //$request->cookie('store_id')
                ]
            );
            $userPermissions = $userPermissions['message']['data'];
            $this->redis->hSet(
                $request->get('user')->user_id,
                'permissions',
                json_encode($userPermissions)
            );
        }

        Auth::user()->setPermissions($userPermissions);

        return $next($request);
    }
}