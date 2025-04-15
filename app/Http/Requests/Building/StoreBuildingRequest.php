<?php

namespace App\Http\Requests;

use App\Enums\BuildingType;
use App\Enums\State;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow the request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'state' => ['required', new Enum(State::class)],
            'building_type' => ['required', new Enum(BuildingType::class)],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}
