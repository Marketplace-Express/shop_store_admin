<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/29
 * Time: 15:32
 */

namespace App\Http\Controllers\Validations;


use App\Http\Controllers\Interfaces\CustomRulesMessagesInterface;
use App\Http\Controllers\Interfaces\RulesInterface;

class AdminLoginRules implements RulesInterface, CustomRulesMessagesInterface
{
    /**
     * @return \string[][]
     */
    public function getRules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required']
        ];
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email',
            'password.required' => 'Password is required'
        ];
    }
}