<?php

namespace App\Livewire\Admin;

use Livewire\Component;

abstract class AdminComponent extends Component
{
    protected array $adminAbilities = [];

    public function boot(): void
    {
        $user = auth()->user();

        abort_unless($user?->canAccessAdminPanel(), 403);

        if ($this->adminAbilities === []) {
            return;
        }

        abort_unless($user->canAny($this->adminAbilities), 403);
    }
}
