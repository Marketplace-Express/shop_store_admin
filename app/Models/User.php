<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as IAuthorizable;
use Illuminate\Contracts\Auth\Authenticatable as IAuthenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * User: Wajdi Jurry
 * Date: 2021/06/29
 * Time: 19:58
 */

class User implements IAuthenticatable, IAuthorizable
{
    use Authenticatable, Authorizable;

    protected $primaryKey = 'user_id';

    /**
     * User constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @param array $permissions
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    /**
     * @return string
     */
    public function adminlte_image()
    {
        return !empty($this->image_uri) ? $this->image_uri : 'https://via.placeholder.com/60x60';
    }

    /**
     * @return string
     */
    public function adminlte_desc()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function adminlte_profile_url()
    {
        return sprintf('%s/user/%s', trim(config('adminlte.dashboard_url'), '/'), $this->user_id);
    }
}