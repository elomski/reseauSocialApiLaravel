<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Requests\AuthRequest\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\AuthInterface;
use App\Models\User;
use App\Responses\ApiResponses;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    private AuthInterface $authInterface;

    public function __construct(AuthInterface $authInterface)
    {
        $this->authInterface = $authInterface;
    }

    public function register(RegisterRequest $registerRequest)
    {
        $data = [
            'name' => $registerRequest->name,
            'email' => $registerRequest->email,
            'password' => $registerRequest->password,
            'password_confirm' => $registerRequest->password_confirm
        ];
        DB::beginTransaction();
        try {
            $user = $this->authInterface->register($data);

            DB::commit();
            return ApiResponses::sendResponse(true, [new UserResource($user)], 'Operation effectue', 201);

        } catch (\Throwable $th) {
            return $th;

            return ApiResponses::rollback($th);
        }
    }

    public function login(LoginRequest $loginRequest)
    {
        $data = [
            'email' => $loginRequest->email,
            'password' => $loginRequest->password,
        ];

        DB::beginTransaction();
        try {
            $user = $this->authInterface->login($data);

            DB::commit();


                if(!$user){
                    return ApiResponses::sendResponse(
                    false,
                    [],
                    'Identifiants invalides.',
                    200);
                }else{
                    return ApiResponses::sendResponse(
                        true,
                        [new UserResource($user)],
                        'Login successfully.',
                        200);
                }




        } catch (\Throwable $th) {
            return $th;
            return ApiResponses::rollback($th);
        }
    }

    public function checkOtpCode(Request $request)
    {
        $data = [
            'email' => $request->email,
            'code' => $request->code,

        ];


        DB::beginTransaction();
        try {
            $user = $this->authInterface->checkOtpCode($data);
            DB::commit();
            if(!$user) {
                return ApiResponses::sendResponse(
                    false,
                    [],
                    'Code de confirmation Invalide.',
                    200
                );

            }
            return ApiResponses::sendResponse(
                true,
                [new UserResource($user)],
                'Operation effectuer.',
                200
            );





        } catch (\Throwable $th) {

            return ApiResponses::rollback($th);
        }
    }
    public function logout()
    {
        $user = User::find(auth()->user()->getAuthIdentifier());
        $user->tokens()->delete();
        return ApiResponses::sendResponse(
            true,
            [],
            'Utilisateurs déconnecté.',
            200
        );

    }


}
