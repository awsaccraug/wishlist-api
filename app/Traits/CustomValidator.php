<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait CustomValidator
{
    public function validator(Request $request, $action)
    {

        switch (strtolower($action)) {
            case 'register':
                $this->validationRules = [
                    'email' => 'required|string| email',
                    'password' => 'required|string|confirmed|min:8|unique:wishers',
                ];
                $this->custValidator = Validator::make($request->all(), $this->validationRules);
                break;
            case 'store':
                $this->validationRules = [
                    'content' => 'required',
                    'name' => 'required'
                ];
                $this->custValidator = Validator::make($request->all(), $this->validationRules);
                break;
            case 'update':
                $this->validationRules = [
                    'content' => 'required'
                ];
                $this->custValidator = Validator::make($request->all(), $this->validationRules);
                break;
            case 'login':
                $this->validationRules = [
                    'email' => 'required',
                    'password' => 'required'
                ];
                $this->custValidator = Validator::make($request->all(), $this->validationRules);
                break;

            default:
                # code...
                break;
        }

        return $this->custValidator;
    }
}
