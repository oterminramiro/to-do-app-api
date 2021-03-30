<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\Task;
use \Firebase\JWT\JWT;

class TaskController extends Controller
{
	public function create(Request $request)
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
					'Name' => 'required_without:Description|string',
					'Description' => 'required_without:Name|string',
					'Priority' => 'numeric',
					'Color' => 'string',
					'Pinned' => 'boolean',
					'Date' => 'date',
				]);

				$Task = new Task;
				$Task->fill($validation);
				$Task->IdCustomer = $Customer->IdCustomer;
				$Task->Guid = Str::uuid()->toString();
				$Task->save();


				$response = array(
					'success' => true,
					'data' => $Task->makeHidden(['IdTask','IdCustomer','created_at','updated_at']),
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

	public function list(Request $request)
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
				$Task = Task::where('IdCustomer',$Customer->IdCustomer)->orderBy('Priority','ASC')->where('Active',1)->get();

				$response = array(
					'success' => true,
					'data' => $Task->makeHidden(['IdTask','IdCustomer','created_at','updated_at']),
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
					'Name' => 'string',
					'Description' => 'string',
					'Priority' => 'numeric',
					'Color' => 'string',
					'Pinned' => 'boolean',
					'Date' => 'date',
					'Guid' => 'UUID|required'
				]);

				$Task = Task::where('Guid',$validation['Guid'])->first();
				$Task->fill($validation);
				$Task->save();

				$response = array(
					'success' => true,
					'data' => $Task->makeHidden(['IdTask','IdCustomer','created_at','updated_at']),
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

	public function delete(Request $request)
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
					'Guid' => 'UUID|required'
				]);

				$Task = Task::where('Guid',$validation['Guid'])->first();
				$Task->Active = 0;
				$Task->save();

				$response = array(
					'success' => true,
					'data' => $Task->Guid,
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
}
