{{-- Footer of an add/edit form: Cancel + Save --}}
@props(['cancel', 'submit' => 'Save'])
<div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
    <a href="{{ $cancel }}" class="btn-secondary">Cancel</a>
    <button type="submit" class="btn-primary">{{ $submit }}</button>
</div>
