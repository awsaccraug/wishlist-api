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
            case 'login':
                $this->validationRules = [
                    'username' => 'required',
                    'password' => 'required'
                ];
                $this->custValidator = Validator::make($request->all(), $this->validationRules);
                break;
            case 'search wish':
                $this->validationRules = [
                    'title' => 'required'
                ];
                $this->custValidator = Validator::make($request->all(), $this->validationRules);
                break;
            case 'add wish':
                $this->validationRules = [
                    'title' => 'required|min:5',
                    'due_date' => 'required|date'
                ];
                $this->custValidator = Validator::make($request->all(), $this->validationRules);
                break;
            case 'update wisher':
                $this->validationRules = [
                    'username' => 'required|string|unique:wishers|min:2',
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
