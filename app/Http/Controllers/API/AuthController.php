<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Mail\HelloEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use GeneralTrait;

    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register']]);
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($this->error(('Validation Error.' . $validator->errors()), 400));
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $success['token'] = $user->createToken('token')->plainTextToken;
        $success['user'] = $user;

        return response()->json($this->success($success, 'User register successfully.', 201));
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            
            $user = Auth::user();

            return response()->json($this->success([
                'user' => $user,
                'authorization' => [
                    'token' => $user->createToken('Token')->plainTextToken,
                    'type' => 'bearer',
                ]
            ], 'User login successfully.', 200));
        }

        return response()->json($this->error(('Unauthorised.' . ['error' => 'Unauthorised']), 401));
    }

    public function logout(Request $request)
    {
        // Auth::user()->tokens()->delete();
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => $request->user(),
        ]);
    }

    public function refresh()
    {
        return response()->json($this->success([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]));
    }

}
