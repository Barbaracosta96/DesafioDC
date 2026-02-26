<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view sales');
    }

    public function view(User $user, Sale $sale): bool
    {
        return $user->can('view sales');
    }

    public function create(User $user): bool
    {
        return $user->can('create sales');
    }

    public function updateStatus(User $user, Sale $sale): bool
    {
        return $user->can('cancel sales');
    }

    public function delete(User $user, Sale $sale): bool
    {
        return $user->hasRole('admin');
    }
}
