<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/07/04
 * Time: 09:08
 */

namespace App\Http\Controllers\Api;


use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends ApiController
{
    /**
     * @var CategoryService
     */
    private $service;

    /**
     * CategoryController constructor.
     * @param CategoryService $service
     */
    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch(Request $request)
    {
        $selections = [
            'id' => 'id',
            'name' => 'name',
            'url' => 'url',
            'order' => 'order',
        ];

        try {
            $response = \response()->json([
                'status' => Response::HTTP_OK,
                'data' => $this->service->fetch(
                    $request->cookie('store_id'),
                    $selections,
                    [$request->get('categoryId')]
                )
            ]);
        } catch (\Throwable $exception) {
            $response = \response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'data' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|Response
     */
    public function updateOrder(Request $request)
    {
        try {
            $this->service->updateOrder([
                'accessToken' => $request->cookie('access_token'),
                'csrfToken' => $request->cookie('csrf_token'),
                'categories' => $request->get('categories'),
                'storeId' => $request->cookie('store_id')
            ]);
            $response = \response(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $exception) {
            $response = \response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'data' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}