<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/07/01
 * Time: 18:18
 */

namespace App\Http\Middleware;


use App\Http\Controllers\Helpers\RequestHelper;
use Illuminate\Http\Request;

class HasStores
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
     * HasStores constructor.
     * @param RequestHelper $requestHelper
     */
    public function __construct(RequestHelper $requestHelper, \Redis $redis)
    {
        $this->requestHelper = $requestHelper;
        $this->redis = $redis;
    }

    public function handle(Request $request, \Closure $next)
    {
        $userStores = json_decode($this->redis->hGet(
            $request->get('user')->user_id,
            'stores')
        );

        if (empty($userStores)) {
            $userStores = $this->requestHelper->call(
                'stores/',
                'GET',
                [],
                [
                    'Authorization' => sprintf('Bearer %s', $request->cookie('access_token')),
                    'csrf-token' => $request->cookie('csrf_token')
                ],
                [
                    'page' => $request->get('page') ?: 1,
                    'limit' => $request->get('limit') ?: 10,
                    'sort' => $request->get('sort') ?: ''
                ]
            );

            $userStores = $userStores['message']['data'];
            $this->redis->hSet(
                $request->get('user')->user_id,
                'stores',
                json_encode($userStores)
            );
        }

        return $next($request);
    }
}