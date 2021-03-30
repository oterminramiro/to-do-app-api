<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Models\Customer;

class TokenMiddleware
{
	public function handle(Request $request, Closure $next)
	{
		try
		{
			$token = $request->bearerToken();
			if(empty($token))
			{
				throw new \Exception("Invalid token");
			}

			$decodedToken = JWT::decode($token, getenv("JWT_SECRET"), array(getenv("JWT_ENC_TYPE")));

			$customer = Customer::where('Guid',$decodedToken->data->guid)->first();
			if($customer == NULL)
			{
				throw new \Exception("Invalid token");
			}
			else
			{
				return $next($request);
			}
		}
		catch (\Exception $e)
		{
			$response = array(
				'success' => false,
				'data' => $e->getMessage(),
				'code' => 500
			);

			return response(json_encode($response), 500)->header('Content-Type', 'application/json');
		}
	}
}
