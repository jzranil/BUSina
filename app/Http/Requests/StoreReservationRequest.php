<?php

namespace App\Http\Requests;

use App\Services\ReservationService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Expected input:
 *   contact_name, contact_number,
 *   passengers[0][seat_number], passengers[0][passenger_name], passengers[1][...] ...
 */
class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Accept "0917 123 4567" or "0917-123-4567"
        $this->merge([
            'contact_number' => preg_replace('/[\s-]/', '', (string) $this->contact_number),
        ]);
    }

    public function rules(): array
    {
        $capacity = $this->route('schedule')->bus->seating_capacity;

        return [
            'contact_name' => ['required', 'string', 'max:100'],
            'contact_number' => ['required', 'regex:/^(09|\+639)\d{9}$/'],
            'passengers' => ['required', 'array', 'min:1', 'max:'.ReservationService::MAX_SEATS_PER_BOOKING],
            'passengers.*.seat_number' => ['required', 'integer', 'min:1', 'max:'.$capacity, 'distinct'],
            'passengers.*.passenger_name' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_number.regex' => 'Enter a Philippine mobile number, like 09171234567 or +639171234567.',
            'passengers.required' => 'Select at least one seat.',
            'passengers.max' => 'You can book up to '.ReservationService::MAX_SEATS_PER_BOOKING.' seats at a time.',
            'passengers.*.seat_number.distinct' => 'Each passenger needs a different seat.',
            'passengers.*.seat_number.max' => 'That seat does not exist on this bus.',
        ];
    }

    public function attributes(): array
    {
        return [
            'passengers.*.seat_number' => 'seat number',
            'passengers.*.passenger_name' => 'passenger name',
        ];
    }
}
