<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'departure_location' => trim((string) $this->departure_location),
            'destination' => trim((string) $this->destination),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $routeId = $this->route('busRoute')?->route_id;

        return [
            'route_name' => ['required', 'string', 'max:100'],
            'departure_location' => ['required', 'string', 'max:100'],
            'destination' => [
                'required', 'string', 'max:100', 'different:departure_location',
                Rule::unique('routes', 'destination')
                    ->where('departure_location', $this->departure_location)
                    ->ignore($routeId, 'route_id'),
            ],
            'distance' => ['required', 'numeric', 'min:1', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'fare' => ['required', 'numeric', 'min:1', 'max:10000'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'destination.different' => 'The destination must be different from the departure location.',
            'destination.unique' => 'A route with this departure location and destination already exists.',
            'duration_minutes.min' => 'Travel time must be at least 15 minutes.',
            'duration_minutes.max' => 'Travel time cannot be more than 24 hours.',
        ];
    }

    public function attributes(): array
    {
        return [
            'duration_minutes' => 'travel time',
            'distance' => 'distance (km)',
        ];
    }
}
