{{--
    2+2 bus seat map with an aisle, driver marker and back bench.
    Read-only (staff):   <x-seat-map :capacity="45" :taken="[3, 4]" />
    Interactive (book):  <x-seat-map :capacity="45" :taken="[3, 4]" interactive />   inside an x-data="seatPicker(...)" element
--}}
@props(['capacity', 'taken' => [], 'interactive' => false])
@php
    // Rows of 2 + aisle + 2; the last row (up to 5 seats) is a bench across the back.
    $rows = [];
    $seat = 1;
    $remaining = (int) $capacity;
    while ($remaining > 5) {
        $rows[] = ['left' => [$seat, $seat + 1], 'right' => [$seat + 2, $seat + 3]];
        $seat += 4;
        $remaining -= 4;
    }
    $back = $remaining > 0 ? range($seat, $seat + $remaining - 1) : [];

    $base = 'flex h-10 w-10 items-center justify-center rounded-lg text-xs font-bold transition sm:h-11 sm:w-11';
    $styles = [
        'available' => 'bg-white text-slate-700 ring-1 ring-slate-300 hover:ring-brand-500 hover:text-brand-700',
        'selected' => 'bg-brand-600 text-white ring-1 ring-brand-700 shadow',
        'taken' => 'bg-slate-200 text-slate-400 ring-1 ring-slate-200 cursor-not-allowed line-through',
    ];
@endphp

@php
    $renderSeat = function (int $n) use ($interactive, $taken, $base, $styles) {
        if ($interactive) {
            return '<button type="button" class="'.$base.'" @click="toggle('.$n.')" :disabled="isTaken('.$n.')"'
                .' :class="isTaken('.$n.') ? \''.$styles['taken'].'\' : (isSelected('.$n.') ? \''.$styles['selected'].'\' : \''.$styles['available'].'\')"'
                .' :aria-pressed="isSelected('.$n.')" aria-label="Seat '.$n.'">'.$n.'</button>';
        }
        $state = in_array($n, $taken, true) ? 'taken' : 'available';

        return '<span class="'.$base.' '.str_replace(['hover:ring-brand-500', 'hover:text-brand-700'], '', $styles[$state]).'" title="Seat '.$n.($state === 'taken' ? ' (taken)' : '').'">'.$n.'</span>';
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-block']) }}>
    <div class="rounded-[2rem] border-2 border-slate-300 bg-slate-50 p-4 sm:p-5">
        {{-- Front of the bus: driver on the left, door on the right --}}
        <div class="mb-4 flex items-center justify-between border-b-2 border-dashed border-slate-300 pb-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 text-white sm:h-11 sm:w-11" title="Driver">
                <x-steering-wheel class="h-6 w-6" />
            </span>
            <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Front</span>
            <span class="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-dashed border-slate-300 text-[10px] font-semibold text-slate-400 sm:h-11 sm:w-11">Door</span>
        </div>

        <div class="space-y-2">
            @foreach ($rows as $row)
                <div class="flex items-center gap-2">
                    @foreach ($row['left'] as $n) {!! $renderSeat($n) !!} @endforeach
                    <span class="w-6 sm:w-8" aria-hidden="true"></span>
                    @foreach ($row['right'] as $n) {!! $renderSeat($n) !!} @endforeach
                </div>
            @endforeach
            @if ($back)
                <div class="flex items-center justify-center gap-2 border-t border-slate-200 pt-2">
                    @foreach ($back as $n) {!! $renderSeat($n) !!} @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Legend --}}
    <div class="mt-4 flex flex-wrap items-center justify-center gap-4 text-xs text-slate-600">
        <span class="flex items-center gap-1.5"><span class="h-4 w-4 rounded bg-white ring-1 ring-slate-300"></span> Available</span>
        @if ($interactive)
            <span class="flex items-center gap-1.5"><span class="h-4 w-4 rounded bg-brand-600"></span> Selected</span>
        @endif
        <span class="flex items-center gap-1.5"><span class="h-4 w-4 rounded bg-slate-200"></span> Occupied</span>
    </div>
</div>
