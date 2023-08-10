<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function response($fn)
    {
        try {

            $response = $fn();

        } catch (\Exception $e) {

            $response['error'] = $e->getMessage();

        }

        return response($response);
    }
}
