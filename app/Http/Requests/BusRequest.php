<?php

namespace App\Http\Requests;

use App\Models\Bus;
use App\Models\ReservationSeat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // access is checked by the role middleware on the route
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'bus_number' => strtoupper(trim((string) $this->bus_number)),
            'plate_number' => strtoupper(preg_replace('/\s+/', ' ', trim((string) $this->plate_number))),
        ]);
    }

    public function rules(): array
    {
        $busId = $this->route('bus')?->bus_id;

        return [
            'bus_number' => ['required', 'string', 'max:20', Rule::unique('buses', 'bus_number')->ignore($busId, 'bus_id')],
            'plate_number' => ['required', 'string', 'max:10', 'regex:/^[A-Z]{3} ?\d{3,4}$/', Rule::unique('buses', 'plate_number')->ignore($busId, 'bus_id')],
            'bus_type' => ['required', Rule::in(array_keys(Bus::TYPES))],
            'seating_capacity' => ['required', 'integer', 'min:10', 'max:60'],
            'driver_name' => ['required', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'status' => ['required', Rule::in(array_keys(Bus::STATUSES))],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $bus = $this->route('bus');
                if (! $bus || $validator->errors()->has('seating_capacity')) {
                    return;
                }

                // Don't let capacity drop below a seat number that is already booked on an upcoming trip.
                $highestBookedSeat = ReservationSeat::where('is_held', true)
                    ->whereHas('schedule', fn ($q) => $q->where('bus_id', $bus->bus_id)->whereDate('departure_date', '>=', today()))
                    ->max('seat_number');

                if ($highestBookedSeat && (int) $this->seating_capacity < $highestBookedSeat) {
                    $validator->errors()->add('seating_capacity', "Seat {$highestBookedSeat} is already booked on an upcoming trip, so capacity cannot go below {$highestBookedSeat}.");
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'plate_number.regex' => 'Enter a plate number like ABC 1234.',
            'driver_name.regex' => 'The driver name may only contain letters, spaces, periods, apostrophes and hyphens.',
            'seating_capacity.min' => 'A bus must have at least 10 seats.',
            'seating_capacity.max' => 'A bus can have at most 60 seats.',
        ];
    }
}
