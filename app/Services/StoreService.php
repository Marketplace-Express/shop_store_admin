<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/07/03
 * Time: 11:17
 */

namespace App\Services;


class StoreService
{
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * StoreService constructor.
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param string $userId
     * @return mixed
     */
    public function getStores(string $userId)
    {
        return json_decode(
            $this->redis->hGet($userId, 'stores')
        );
    }

    /**
     * @param string $storeId
     * @param string $userId
     */
    public function updateLastLogin(string $storeId, string $userId)
    {
        $userStores = json_decode($this->redis->hGet($userId, 'stores'), true);

        if (is_array($userStores)) {
            foreach ($userStores as &$store) {
                if ($store['storeId'] == $storeId) {
                    $store['lastLogin'] = time();
                    break;
                }
            }

            $this->redis->hSet($userId, 'stores', json_encode($userStores));
        }
    }
}