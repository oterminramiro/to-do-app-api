<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Models\Customer;
use \Firebase\JWT\JWT;

class CustomerController extends Controller
{
	public function create(Request $request)
	{
		try
		{
			$validation = $this->validate($request, [
				'name' => 'string',
				'lastname' => 'string',
				'email' => 'required|email',
				'phone' => 'required_without:email|numeric',
				'password' => 'required|string',
			]);

			$Customer = new Customer;
			$Customer->Name = $validation['name'];
			$Customer->Lastname = $validation['lastname'];
			$Customer->Email = $validation['email'];
			$Customer->Phone = array_key_exists('phone',$validation) ? $validation['phone'] : NULL;
			$Customer->Password = Crypt::encrypt($validation['password']);
			$Customer->Guid = Str::uuid()->toString();
			$Customer->Active = 0;
			$Customer->save();

			$response = array(
				'success' => true,
				'data' => $Customer->Guid,
				'code' => 200
			);

			return response(json_encode($response), 200)->header('Content-Type', 'application/json');
		}
		catch (Exception $e)
		{
			$response = array(
				'success' => false,
				'data' => $e->getMessage(),
				'code' => 500
			);

			return response(json_encode($response), 500)->header('Content-Type', 'application/json');
		}
	}

	public function login(Request $request)
	{
		try
		{
			$validation = $this->validate($request, [
				'email' => 'required|email',
				'phone' => 'required_without:email|numeric',
				'password' => 'required|string',
			]);

			if(array_key_exists('email',$validation))
			{
				$Customer = Customer::where('Email',$validation['email'])->first();
			}
			else
			{
				$Customer = Customer::where('Phone',$validation['phone'])->first();
			}

			if($Customer == NULL)
			{
				$response = array(
					'success' => false,
					'data' => 'Customer not found',
					'code' => 404
				);

				return response(json_encode($response), 404)->header('Content-Type', 'application/json');
			}
			try
			{
				$decrypted = Crypt::decrypt($Customer->Password);
			}
			catch (DecryptException $e)
			{
				$response = array(
					'success' => false,
					'data' => $e->getMessage(),
					'code' => 500
				);

				return response(json_encode($response), 500)->header('Content-Type', 'application/json');
			}
			if($decrypted == $validation['password'])
			{
				$token = $this->_generateToken($Customer);
			}

			$Customer->Active = 1;
			$Customer->save();

			$response = array(
				'success' => true,
				'data' => array(
					'token' => $token,
					'guid' => $Customer->Guid
				),
				'code' => 200
			);

			return response(json_encode($response), 200)->header('Content-Type', 'application/json');
		}
		catch (Exception $e)
		{
			$response = array(
				'success' => false,
				'data' => $e->getMessage(),
				'code' => 500
			);

			return response(json_encode($response), 500)->header('Content-Type', 'application/json');
		}
	}

	public function show(Request $request)
	{
		try
		{
			$token = $request->bearerToken();

			$decoded = JWT::decode($token, getenv('JWT_SECRET'), array(getenv('JWT_ENC_TYPE')));

			$Customer = Customer::where('Guid',$decoded->data->guid)->first();

			if($Customer == NULL)
			{
				$response = array(
					'success' => false,
					'data' => 'Customer not found',
					'code' => 404
				);

				return response(json_encode($response), 404)->header('Content-Type', 'application/json');
			}
			else
			{
				$response = array(
					'success' => true,
					'data' => $Customer->makeHidden(['IdCustomer','Password','created_at','updated_at']),
					'code' => 200
				);

				return response(json_encode($response), 200)->header('Content-Type', 'application/json');
			}
		}
		catch (Exception $e)
		{
			$response = array(
				'success' => false,
				'data' => $e->getMessage(),
				'code' => 500
			);

			return response(json_encode($response), 500)->header('Content-Type', 'application/json');
		}
	}

	public function edit(Request $request)
	{
		try
		{
			$token = $request->bearerToken();

			$decoded = JWT::decode($token, getenv('JWT_SECRET'), array(getenv('JWT_ENC_TYPE')));

			$Customer = Customer::where('Guid',$decoded->data->guid)->first();

			if($Customer == NULL)
			{
				$response = array(
					'success' => false,
					'data' => 'Customer not found',
					'code' => 404
				);

				return response(json_encode($response), 404)->header('Content-Type', 'application/json');
			}
			else
			{
				$validation = $this->validate($request, [
					'name' => 'string',
					'lastname' => 'string',
					'email' => 'email',
					'phone' => 'numeric',
					'password' => 'string',
				]);

				if(array_key_exists('name',$validation))
				{
					$Customer->Name = $validation['name'];
				}
				if(array_key_exists('lastname',$validation))
				{
					$Customer->Lastname = $validation['lastname'];
				}
				if(array_key_exists('email',$validation))
				{
					$Customer->Email = $validation['email'];
				}
				if(array_key_exists('phone',$validation))
				{
					$Customer->Phone = $validation['phone'];
				}
				if(array_key_exists('password',$validation))
				{
					$Customer->Password = Crypt::encrypt($validation['password']);
				}

				$Customer->save();

				$response = array(
					'success' => true,
					'data' => $Customer->makeHidden(['IdCustomer','Password','created_at','updated_at']),
					'code' => 200
				);

				return response(json_encode($response), 200)->header('Content-Type', 'application/json');
			}
		}
		catch (Exception $e)
		{
			$response = array(
				'success' => false,
				'data' => $e->getMessage(),
				'code' => 500
			);

			return response(json_encode($response), 500)->header('Content-Type', 'application/json');
		}
	}

	private function _generateToken($Customer)
	{
		$t = time();
		$payload = array(
			"iss" => '',
			"iat" => $t,
			"nbf" => $t,
			"exp" => $t + 31536000,
			"aud" => "api_user",
			"data" => array(
				"guid" => $Customer->Guid
			)
		);
		$jwt = JWT::encode($payload, getenv("JWT_SECRET"));

		return $jwt;
	}
}
