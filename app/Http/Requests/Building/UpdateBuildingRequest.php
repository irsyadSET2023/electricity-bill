<?php

namespace App\Http\Requests\Building;

use App\Enums\BuildingType;
use App\Enums\State;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow the update request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'state' => ['sometimes', new Enum(State::class)],
            'building_type' => ['sometimes', new Enum(BuildingType::class)],
        ];
    }
}
