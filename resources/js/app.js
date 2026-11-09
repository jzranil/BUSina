import './bootstrap';

// Alpine comes bundled with Livewire (loaded by @livewireScripts in the layouts).
document.addEventListener('alpine:init', () => {
    const KEY = 'busina.sidebar.collapsed';

    // Staff portal sidebar: collapsible on desktop (remembered), drawer on mobile.
    window.Alpine.data('sidebar', () => ({
        collapsed: (() => {
            try { return localStorage.getItem(KEY) === '1'; } catch (e) { return false; }
        })(),
        mobileOpen: false,
        toggle() {
            this.collapsed = !this.collapsed;
            try { localStorage.setItem(KEY, this.collapsed ? '1' : '0'); } catch (e) { /* storage blocked: still works for this page */ }
        },
    }));

    // Customer seat map: pick up to `max` seats, one passenger name per seat.
    window.Alpine.data('seatPicker', ({ max, taken, initial }) => ({
        max,
        taken,
        passengers: initial, // [{ seat_number, passenger_name }]
        notice: '',
        isTaken(seat) { return this.taken.includes(seat); },
        isSelected(seat) { return this.passengers.some((p) => p.seat_number === seat); },
        toggle(seat) {
            if (this.isTaken(seat)) return;
            this.notice = '';
            if (this.isSelected(seat)) {
                this.passengers = this.passengers.filter((p) => p.seat_number !== seat);
            } else if (this.passengers.length >= this.max) {
                this.notice = `You can only pick ${this.max} seat${this.max > 1 ? 's' : ''}. Increase the number of passengers or unselect a seat.`;
            } else {
                this.passengers.push({ seat_number: seat, passenger_name: '' });
                this.passengers.sort((a, b) => a.seat_number - b.seat_number);
            }
        },
        setMax(value) {
            this.max = Number(value);
            if (this.passengers.length > this.max) this.passengers = this.passengers.slice(0, this.max);
            this.notice = '';
        },
    }));
});
