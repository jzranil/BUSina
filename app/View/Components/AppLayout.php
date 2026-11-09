<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * <x-app-layout> picks the layout by role:
 *   admin / employee → staff portal with sidebar
 *   customer         → site layout with the top bar
 * Jetstream's profile pages use <x-app-layout>, so they follow the same rule.
 */
class AppLayout extends Component
{
    public function __construct(public ?string $title = null) {}

    public function render(): View
    {
        return auth()->user()?->isStaff()
            ? view('layouts.portal')
            : view('layouts.site');
    }
}
