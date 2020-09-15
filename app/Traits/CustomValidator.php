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
                    'username' => 'required|string|unique:wishers|min:2',
                    'password' => 'required|string|confirmed|min:8',
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
                    'username' => 'required',
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
