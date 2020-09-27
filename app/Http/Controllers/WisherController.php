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
use Illuminate\Support\Facades\Storage;

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
            $wisher = Wisher::where('id', $id)->first();
            if (!$wisher) {
                return $this->response($this->notfoundStatusCode, $this->notfoundMessage, []);
            }
            $path = $this->updateUploadedFile($request);
            $wisher->update(array_merge($request->except('api_token'), ['profile_photo' => $path]));
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
    public function removeProfilePhoto($id)
    {
        try {
            $wisher = Wisher::where('id', $id)->first();
            if (!$wisher) {
                return $this->response($this->notfoundStatusCode, $this->notfoundMessage, []);
            }
            $wisher->update(['profile_photo' => null]);
            return $this->response($this->okStatusCode, $this->okMessage, $wisher);
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
                return $this->response($this->errorStatusCode, $this->errorMessage, $validatedData->messages()->all());
            }
            $path = $this->storeUploadedFile($request);
            $wisher = Wisher::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'profile_photo' => $path
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
    protected function storeUploadedFile(Request $request)
    {
        if ($request->hasFile('profile_photo')) {
            return $request->file('profile_photo')->store('wishers', 's3');
        }
    }
    protected function updateUploadedFile(Request $request)
    {
        $this->removeExistingFile($request);
        return !$request->hasFile('profile_photo') && $request->path ? $request->path : $this->storeUploadedFile($request);
    }
    protected function removeExistingFile(Request $request)
    {
        if ($request->hasFile('profile_photo') && $request->path) {
            Storage::disk('s3')->exists($request->path) ? Storage::disk('s3')->delete($request->path) : "";
        }
    }
}
