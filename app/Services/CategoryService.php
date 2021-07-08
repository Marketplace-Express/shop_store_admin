<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/07/04
 * Time: 09:08
 */

namespace App\Services;


use App\Http\Controllers\Helpers\RequestHelper;

class CategoryService
{
    /**
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * CategoryService constructor.
     * @param RequestHelper $requestHelper
     */
    public function __construct(RequestHelper $requestHelper)
    {
        $this->requestHelper = $requestHelper;
    }

    /**
     * @param string $storeId
     * @param array $selections
     * @param array $categoriesIds
     * @return array
     */
    public function fetch(string $storeId, array $selections = [], array $categoriesIds = [])
    {
        $categories = $this->requestHelper->call(
            'categories/fetch',
            'POST',
            [
                'selections' => $selections,
                'categories_ids' => array_filter($categoriesIds)
            ],
            [
                'storeId' => $storeId
            ]
        );

        return $categories['message']['data'];
    }

    /**
     * @param array $categories
     * @param string $storeId
     */
    public function updateOrder(array $data = [])
    {
        $this->requestHelper->call(
            'categories/updateOrder',
            'POST',
            [
                'categories' => $data['categories']
            ],
            [
                'Authorization' => sprintf('Bearer %s', $data['accessToken']),
                'csrf-token' => $data['csrfToken'],
                'storeId' => $data['storeId']
            ]
        );
    }
}