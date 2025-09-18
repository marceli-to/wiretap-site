<?php

namespace App\Http\Controllers;

use App\Actions\StoreLogAction;
use App\Http\Requests\StoreLogRequest;

class WebhookController extends Controller
{
	public function store(StoreLogRequest $request, StoreLogAction $action)
	{
		$action->execute($request->validated());

		return response()->json(['status' => 'success'], 201);
	}
}
