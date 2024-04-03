<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            abort(422, $validator->errors()->toJson());
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Verificar se o usuário está ativo
            if (!$user->active) {
                Auth::logout();
                abort(403,  __('messages.login.account_inactive'));
            }

            // Se todas as verificações passaram, criar e retornar o token de acesso
            $data['token'] = $user->createToken('MyApp')->plainTextToken;
            $data['name'] = $user->name;

            return response()->json([
                'data' => $data
            ]);
        } else {
            abort(401, __('messages.login.invalid_credentials'));
        }
    }

}
