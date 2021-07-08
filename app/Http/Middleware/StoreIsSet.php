<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/07/03
 * Time: 14:12
 */

namespace App\Http\Middleware;


use Illuminate\Http\Request;

class StoreIsSet
{
    public function handle(Request $request, \Closure $next)
    {
        $storeId = $request->cookie('store_id');

        if (!isset($storeId) || empty($storeId)) {
            return redirect('/stores');
        }

        return $next($request);
    }
}