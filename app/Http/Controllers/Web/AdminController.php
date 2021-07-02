<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/29
 * Time: 14:57
 */

namespace App\Http\Controllers\Web;


use App\Exceptions\NotFound;
use App\Exceptions\OperationNotPermitted;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Validations\AdminLoginRules;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;
use JeroenNoten\LaravelAdminLte\AdminLte;

class AdminController extends Controller
{
    /**
     * @var AdminLte
     */
    private $adminLte;
    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(AdminLte $adminLte, AuthService $authService)
    {
        $this->adminLte = $adminLte;
        $this->authService = $authService;
    }

    public function index()
    {
        return view('custom.page', [
            'adminlte' => $this->adminLte
        ]);
    }

    public function new()
    {
        return view('custom.new', [
            'adminlte' => $this->adminLte
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(Request $request)
    {
        try {
            $redirect = $request->query('redirect');

            $this->validation($request, new AdminLoginRules());
            $user = $this->authService->authenticate(
                $request->get('email'),
                $request->get('password')
            );

            Cookie::queue('access_token', $user->access_token);
            Cookie::queue('refresh_token', $user->refresh_token);
            Cookie::queue('csrf_token', $user->csrf_token);

            return redirect($redirect ?: '/');
        } catch (ValidationException $exception) {
            return back($exception->getCode())->withErrors($exception->errors());
        } catch (NotFound | OperationNotPermitted $exception) {
            return back()->withErrors(['error_msg' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            return back(Response::HTTP_INTERNAL_SERVER_ERROR)->withErrors(['error_msg' => $exception->getMessage()]);
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        try {
            $this->authService->deAuthenticate();

            return redirect('/login')
                ->withCookie(Cookie::forget('access_token'))
                ->withCookie(Cookie::forget('refresh_token'))
                ->withCookie(Cookie::forget('csrf_token'));
        } catch (\Throwable $exception) {
            return back()->withErrors(['error_msg' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function loginView(Request $request)
    {
        if ($request->hasCookie('access_token')) {
            return back();
        } else {
            return view('login');
        }
    }
}