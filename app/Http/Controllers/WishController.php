<?php

namespace App\Http\Controllers;

use App\Todo;
use App\Traits\CustomResponse;
use App\Wish;
use App\Wishes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WishController extends Controller
{
    use CustomResponse;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index()
    {
        $wishes = Wish::with('wisher')->latest()->get();
        return $this->response($this->okStatusCode, $this->okMessage, $wishes);
    }
    public function getWishesForWisher()
    {
        $wishes = Auth::user()->wishes()->latest()->get();
        return $this->response($this->okStatusCode, $this->okMessage, $wishes);
    }
    public function search(Request $request)
    {
        $wishes = Wish::where('title', 'LIKE', '%' . $request->title . '%')->with('wisher')->latest()->get();
        return $this->response($this->okStatusCode, $this->okMessage, $wishes);
    }
    public function getWish($id)
    {
        try {
            $wish = Wish::where('id', $id)->first();
            if (!$wish) {
                return $this->response($this->notfoundStatusCode, $this->notfoundMessage, []);
            }
            return $this->response($this->okStatusCode, $this->okMessage, $wish);
        } catch (\Exception $e) {
            return $this->response($this->serverErrorStatusCode, $this->serverErrorMessage, []);
            Log::warning(['Exception => ' => $e->getMessage()]);
        }
    }
    public function addWish(Request $request)
    {
        try {
            $wish = Wish::create(array_merge($request->all(), ['wisher_id' => Auth::id()]));
            if ($wish) {
                return $this->response($this->createdStatusCode, $this->createdMessage, $wish);
            }
        } catch (\Exception $e) {
            return $this->response($this->serverErrorStatusCode, $this->serverErrorMessage, []);
            Log::warning(['Exception => ' => $e->getMessage()]);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $wish = Wish::where('id', $id)->first();
            if (!$wish) {
                return $this->response($this->notfoundStatusCode, $this->notfoundMessage, []);
            }
            $wish->update($request->except('api_token'));
            return $this->response($this->okStatusCode, $this->okMessage, $wish);
        } catch (\Exception $e) {
            return $this->response($this->serverErrorStatusCode, $this->serverErrorMessage, []);
            Log::warning(['Exception => ' => $e->getMessage()]);
        }
    }
    public function delete($id)
    {
        try {
            Wish::destroy($id);
            return $this->response($this->deletedStatusCode, $this->deletedMessage, []);
        } catch (\Exception $e) {
            return $this->response($this->serverErrorStatusCode, $this->serverErrorMessage, []);
            Log::warning(['Exception => ' => $e->getMessage()]);
        }
    }

    //
}
