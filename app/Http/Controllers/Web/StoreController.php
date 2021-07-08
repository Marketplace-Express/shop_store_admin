<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/07/03
 * Time: 11:11
 */

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Services\StoreService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use JeroenNoten\LaravelAdminLte\AdminLte;

class StoreController extends Controller
{
    /**
     * @var AdminLte
     */
    private $adminLte;

    /**
     * @var StoreService
     */
    private $service;

    /**
     * StoreController constructor.
     * @param AdminLte $adminLte
     */
    public function __construct(AdminLte $adminLte, StoreService $service)
    {
        $this->adminLte = $adminLte;
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getStores()
    {
        try {
            return view('stores', [
                'stores' => $this->service->getStores(Auth::user()->user_id)
            ]);
        } catch (\Throwable $exception) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /**
     * @param string $storeId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function manageStore(string $storeId, Request $request)
    {
        try {
            $this->service->updateLastLogin($storeId, $request->get('user')->user_id);
            return redirect('/')->withCookie('store_id', $storeId);
        } catch (\Throwable $exception) {
            return back()->withErrors(['error_msg' => $exception->getMessage()]);
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function changeStore()
    {
        try {
            return redirect('/stores')
                ->withCookie(Cookie::forget('store_id'));
        } catch (\Throwable $exception) {
            return back()->withErrors(['error_msg' => $exception->getMessage()]);
        }
    }
}