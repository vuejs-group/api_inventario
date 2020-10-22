<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /*
		Registro de usuarios
	*/
	public function signup(Request $request)
	{
		$request->validate([
			'name' => 'required|string',
			'email' => 'required|string|email|unique:users',
			'password' => 'required|string|confirmed',
		]);
		
		$user = new User([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
		]);
		
		$user->save();
		return response()->json([
		'message' => 'Usuario creado exitosamente!'], 201);
	}
	
	public function login(Request $request)
	{
		$request->validate([
			'email' => 'string|email',
			'name' => 'string',
			'username' => 'required|string',
			'password' => 'required|string',
			'remember_me' => 'boolean',
		]);
		
		#$credentials = request(['email', 'password']);
		$credentials = request(['username', 'password']);
		
		//Log::info($credentials);
		
		if (!Auth::attempt($credentials)) {
			return response()->json([
			'message' => 'Unauthorized'], 401);
		}
		
		$user = $request->user();
		$tokenResult = $user->createToken('Personal Access Token');
		$token = $tokenResult->token;
		
		if ($request->remember_me) {
			$token->expires_at = Carbon::now()->addWeeks(1);
		}
		
		#$name = DB::table('users')->select('name')->where('email',$request->email)->get();
		$name = DB::table('users')->select(DB::raw('name'))->where('username',$request->username)->get();
		
		$token->save();
		return response()->json([
			'access_token' => $tokenResult->accessToken,
			'email' => $request->email,
			'username' => $request->username,
			'name' => $name[0]->name,
			'token_type' => 'Bearer',
			'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
		]);
	}
	
	public function logout(Request $request)
	{
		$request->user()->token()->revoke();
		return response()->json(['message' => 'Sesión cerrada correctamente!']);
	}
	
	public function user(Request $request)
	{
		return response()->json($request->user());
	}
}
