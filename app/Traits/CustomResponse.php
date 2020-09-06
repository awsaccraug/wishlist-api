<?php

namespace App\Traits;

trait CustomResponse
{
    protected $okMessage = 'Success';
    protected $okStatusCode = 200;

    protected $createdMessage = 'Created';
    protected $createdStatusCode = 201;

    protected $updatedMessage = 'Updated';

    protected $deletedMessage = 'No Content';
    protected $deletedStatusCode = 204;

    protected $errorMessage = 'An Error Occurred';
    protected $errorStatusCode = 400;

    protected $serverErrorMessage = 'Internal Server Error';
    protected $serverErrorStatusCode = 500;

    protected $notfoundMessage = 'Record Not found';
    protected $notfoundStatusCode = 404;

    protected $unauthorizedMessage = 'Unauthorized';
    protected $unauthorizedStatusCode = 401;

    public function response($code, $message, $data = [])
    {
        $type = 'errors';
        if ($code == 200 || $code == 201) {
            $type = 'data';
        }
        return response()->json([
            "message" => $message,
            "code" => $code,
            $type => $data,
        ], 200);
    }
}
