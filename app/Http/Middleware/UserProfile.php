<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/30
 * Time: 15:03
 */

namespace App\Http\Middleware;


use App\Http\Controllers\Helpers\RequestHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfile
{
    /**
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * UserProfile constructor.
     * @param RequestHelper $requestHelper
     */
    public function __construct(RequestHelper $requestHelper)
    {
        $this->requestHelper = $requestHelper;
    }

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $userData = session('user_data');

        if (empty($userData)) {
            $userData = $this->requestHelper->call(
                'users/profile',
                'GET',
                [],
                [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'csrf-token' => $request->cookie('csrf_token')
                ]
            );

            $userData = $userData['message']['data'];
            $userData['name'] = sprintf('%s %s', $userData['first_name'], $userData['last_name']);
            session(['user_data' => $userData]);
        }


        Auth::setUser(new User($userData));

        $request->merge(['user' => $user = Auth::user()]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}