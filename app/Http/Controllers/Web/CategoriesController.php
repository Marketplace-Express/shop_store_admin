<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/07/04
 * Time: 21:11
 */

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use JeroenNoten\LaravelAdminLte\AdminLte;

class CategoriesController extends Controller
{
    /**
     * @var CategoryService
     */
    private $service;

    /**
     * @var AdminLte
     */
    private $adminLte;

    /**
     * CategoriesController constructor.
     * @param AdminLte $adminLte
     * @param CategoryService $service
     */
    public function __construct(AdminLte $adminLte, CategoryService $service)
    {
        $this->service = $service;
        $this->adminLte = $adminLte;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function listView()
    {
        return view('categories.list', [
            'adminlte' => $this->adminLte
        ]);
    }
}