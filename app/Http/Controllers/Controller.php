<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Interfaces\CustomRulesMessagesInterface;
use App\Http\Controllers\Interfaces\RulesInterface;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @param RulesInterface $rules
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validation(Request $request, RulesInterface $rules)
    {
        $messages = [];
        if ($rules instanceof CustomRulesMessagesInterface) {
            $messages = $rules->getMessages();
        }

        $validator = Validator::make($request->all(), $rules->getRules(), $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

}
