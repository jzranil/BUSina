{{-- Public + customer layout (top bar). Usage: <x-site-layout title="Search Trips"> ... </x-site-layout> --}}
@props(['title' => null])
@include('layouts.site', ['title' => $title])
