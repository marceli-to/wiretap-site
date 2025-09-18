<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'timestamp' => 'required|string',
			'level' => 'required|string',
			'message' => 'required|string',
			'context' => 'nullable|array',
			'app.name' => 'nullable|string',
			'app.env' => 'nullable|string',
			'app.url' => 'nullable|string',
			'server.hostname' => 'nullable|string',
			'server.ip' => 'nullable|string',
		];
	}
}
