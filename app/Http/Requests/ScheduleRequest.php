<?php

namespace App\Http\Requests;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bus_id' => ['required', 'integer', Rule::exists('buses', 'bus_id')->where('status', 'active')],
            'route_id' => ['required', 'integer', Rule::exists('routes', 'route_id')->where('is_active', true)],
            'departure_date' => ['required', 'date_format:Y-m-d'],
            'departure_time' => ['required', 'date_format:H:i'],
            'status' => ['sometimes', 'required', Rule::in(array_keys(Schedule::STATUSES))],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $schedule = $this->route('schedule');
                $status = $this->input('status', $schedule?->status ?? 'scheduled');
                $route = BusRoute::find($this->route_id);
                $departs = Carbon::parse($this->departure_date.' '.$this->departure_time);
                $arrives = $departs->copy()->addMinutes($route->duration_minutes);

                if ($status === 'scheduled' && $departs->isPast()) {
                    $validator->errors()->add('departure_date', 'The departure date and time must be in the future.');

                    return;
                }

                // The same bus cannot be on two trips at once.
                $clash = Schedule::with('route')
                    ->where('bus_id', $this->bus_id)
                    ->where('status', '!=', 'canceled')
                    ->when($schedule, fn ($q) => $q->where('schedule_id', '!=', $schedule->schedule_id))
                    ->whereDate('departure_date', '>=', $departs->copy()->subDay()->toDateString())
                    ->whereDate('departure_date', '<=', $arrives->toDateString())
                    ->get()
                    ->first(fn (Schedule $other) => $other->departsAt()->lt($arrives) && $other->arrivesAt()->gt($departs));

                if ($clash) {
                    $validator->errors()->add('bus_id', 'This bus is already assigned to '.$clash->label().' at that time.');
                }

                // When moving a trip to a smaller bus, existing bookings must still fit.
                if ($schedule && (int) $this->bus_id !== (int) $schedule->bus_id) {
                    $highestBookedSeat = $schedule->heldSeats()->max('seat_number');
                    $capacity = Bus::find($this->bus_id)->seating_capacity;
                    if ($highestBookedSeat && $capacity < $highestBookedSeat) {
                        $validator->errors()->add('bus_id', "Seat {$highestBookedSeat} is already booked on this trip; the selected bus only has {$capacity} seats.");
                    }
                }
            },
        ];
    }

    /** Validated data with the time normalised to HH:MM:SS. */
    public function scheduleData(): array
    {
        $data = $this->validated();
        $data['departure_time'] = Carbon::createFromFormat('H:i', $data['departure_time'])->format('H:i:s');

        return $data;
    }

    public function messages(): array
    {
        return [
            'bus_id.exists' => 'Choose a bus that is currently active.',
            'route_id.exists' => 'Choose a route that is currently active.',
            'departure_date.date_format' => 'Enter a valid departure date.',
            'departure_time.date_format' => 'Enter a valid departure time.',
        ];
    }

    public function attributes(): array
    {
        return [
            'bus_id' => 'bus',
            'route_id' => 'route',
        ];
    }
}
