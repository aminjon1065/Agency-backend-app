<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class Authentification extends Controller
{
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'department' => 'required',
            'position' => 'required',
            'rank' => 'required',
            'avatar' => 'image',
            'signature' => 'image'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 422);
        }
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarName = $request->name . '-' . time() . '.' . $avatarFile->getClientOriginalExtension();
            $avatarPath = public_path('avatars');
            $avatarFile->move($avatarPath, $avatarName);
        } else $avatarName = null;

        if ($request->hasFile('signature')) {
            $signatureFile = $request->file('signature');
            $signatureName = $request->name . '-' . time() . '.' . $signatureFile->getClientOriginalExtension();
            $signaturePath = public_path('signatures');
            $signatureFile->move($signaturePath, $signatureName);
        } else $signatureName = null;
        $data = [
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'department' => $request['department'],
            'position' => $request['position'],
            'rank' => $request['rank'],
            'avatar' => URL::to("/") . "/avatars/" . $avatarName,
            'signature' => URL::to("/") . "/signatures/" . $signatureName
        ];
        $user = User::create($data);
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Успешно зарегистрировано'
            ], 201);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Непредвиденная ошибка'
        ], 409);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Успешно вышли',
        ], 204);
    }

    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email',
            'password' => '|required|string|min:6',
        ]);

        if (!Auth::attempt($attr)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Не правильный E-mail или пароль'
            ], 401);
        }

        $token = auth()->user()->createToken('__sign_token')->plainTextToken;
        $user = $this->isAuth($token);
        return response()->json([
            'status' => 'success',
            'message' => 'Успешно вошли в систему',
            'token' => $token,
            'data' => $user
        ], 201);
    }

    public function isAuth()
    {
        $userAllInfo = \auth()->user();
        $user['name'] = $userAllInfo['name'];
        $user['email'] = $userAllInfo['email'];
        $user['rank'] = $userAllInfo['rank'];
        $user['position'] = $userAllInfo['position'];
        $user['department'] = $userAllInfo['department'];
        $user['region'] = $userAllInfo['region'];
        $user['signature'] = $userAllInfo['signature'];
        $user['avatar'] = $userAllInfo['avatar'];
        return $user;
    }
}
