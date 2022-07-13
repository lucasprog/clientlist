<?php

namespace App\Http\Controllers;

use App\Models\Clients as ClientsModel;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    private $model;
    private $defaultMessageError = [
        'message' => 'Oops, ocorreu um erro interno, tente novamente!'
    ];
    
    /**
     * ClientsController.
     *
     * @return void
     */
    public function __construct(ClientsModel $model)
    {
        $this->model = $model;
    }

    public function sigIn(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

}
