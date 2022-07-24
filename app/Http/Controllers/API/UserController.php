<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use App\Models\User;
use App\Models\State;

class UserController extends Controller
{
    /**
    * Register a newly user in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'name' => 'required|string',
          'email' => 'required|email|unique:users',
          'phone_number' => 'required|digits:10',
          'state_id' => 'required|integer|exists:states,id',
          'password' => 'required|string|min:6',
          'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
          return response()->json([
            'success' => false,
            'message' => $validator->errors()
          ], 422);
        }

        $user = User::create([
          'name' => $request->name,
          'email' => $request->email,
          'phone_number' => $request->phone_number,
          'state_id' => $request->state_id,
          'password' => Hash::make($request->password)
        ]);

        return response()->json([
          'success' => true,
          'message' => 'User registered succesfully.',
          'data' => $user
        ], 200);

    }

    /**
    * Login the specified user
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'email' => 'required|email|exists:users',
          'password' => 'required',
        ]);

        if ($validator->fails()) {
          return response()->json([
            'success' => false,
            'message' => $validator->errors(),
          ], 422);
        }

        if (auth()->attempt($request->all())) {
          $token = auth()->user()->createToken('passport_token')->accessToken;

          return response()->json([
            'success' => true,
            'message' => 'User login succesfully, Use token to authenticate.',
            'data' => auth()->user(),
            'token' => $token
          ], 200);

        } else {
          return response()->json([
            'success' => false,
            'message' => 'User authentication failed.'
          ], 401);
        }
    }


    /**
    * Display the specified user.
    *
    * @return \Illuminate\Http\Response
    */
    public function getUser()
    {
      $user = User::with('state')->find(Auth::id());

      return response()->json([
        'success' => true,
        'message' => 'User profile fetched successfully.',
        'data' => $user
      ], 200);
    }

    /**
    * Logout user.
    *
    * @return \Illuminate\Http\Response
    */
    public function logout()
    {
      $access_token = auth()->user()->token();

      //logout from only current device
      $tokenRepository = app(TokenRepository::class);
      $tokenRepository->revokeAccessToken($access_token->id);

      // use this method to logout from all devices
      $refreshTokenRepository = app(RefreshTokenRepository::class);
      $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($access_token->id);

      return response()->json([
        'success' => true,
        'message' => 'User logout successfully.'
      ], 200);
    }

}
