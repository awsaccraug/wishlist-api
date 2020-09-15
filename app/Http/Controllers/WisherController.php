<?php

namespace App\Http\Controllers;

use App\Traits\CustomResponse;
use App\Traits\CustomValidator;
use App\User;
use App\Wisher;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WisherController extends Controller
{
    use CustomValidator, CustomResponse;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
        $wishers = Wisher::all();
        return $this->response($this->okStatusCode, $this->okMessage, $wishers);
    }
    public function getUser($id)
    {
        $wisher = Wisher::find($id)->first();
        if (!$wisher) {
            return $this->response($this->notfoundStatusCode, $this->notfoundMessage, []);
        }
        return $this->response($this->okStatusCode, $this->okMessage, $wisher);
    }
    public function update(Request $request, $id)
    {
        try {
            $wisher = Wisher::find($id)->first();
            if (!$wisher) {
                return $this->response($this->notfoundStatusCode, $this->notfoundMessage, []);
            }
            $wisher->update($request->all());
            return $this->response($this->okStatusCode, $this->okMessage, $wisher);
        } catch (\Exception $e) {
            return $this->response($this->serverErrorStatusCode, $this->serverErrorMessage, []);
            Log::warning(['Exception => ' => $e->getMessage()]);
        }
    }
    public function delete($id)
    {
        try {
            Wisher::destroy($id);
            return $this->response($this->deletedStatusCode, $this->deletedMessage, []);
        } catch (\Exception $e) {
            return $this->response($this->serverErrorStatusCode, $this->serverErrorMessage, []);
            Log::warning(['Exception => ' => $e->getMessage()]);
        }
    }
    public function login(Request $request)
    {
        try {
            $validatedData = $this->validator($request, 'login');
            if ($validatedData->fails()) {
                return $this->response($this->errorStatusCode, $this->errorMessage, $validatedData->errors());
            }
            $wisher = Wisher::where('username', $request->username)->first();
            if (!$wisher) {
                return $this->response($this->notfoundStatusCode, $this->notfoundMessage, []);
            }
            if (Hash::check($request->password, $wisher->password)) {
                $wisher->api_token = Str::random(40);
                $wisher->save();
                return $this->response($this->okStatusCode, $this->okMessage, $wisher);
            }
        } catch (\Exception $e) {
            return $this->response($this->serverErrorStatusCode, $this->serverErrorMessage, []);
            Log::warning(['Exception => ' => $e->getMessage()]);
        }
    }
    public function register(Request $request)
    {
        try {
            $validatedData = $this->validator($request, 'register');
            if ($validatedData->fails()) {
                return $this->response($this->errorStatusCode, $this->errorMessage, $validatedData->errors());
            }
            $wisher = Wisher::create([
                'username' => $request['username'],
                'password' => Hash::make($request['password']),
            ]);
            if ($wisher) {
                return $this->login($request);
            }
        } catch (QueryException $e) {
            Log::warning(['Error => ' => $e->getMessage()]);
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return $this->response($errorCode, 'The username has already been taken', []);
            }
        } catch (Exception $e) {
            Log::warning(['Exception =>' => $e->getMessage()]);
            return $this->response($this->serverErrorStatusCode, $this->serverErrorMessage, []);
        }
    }
}
