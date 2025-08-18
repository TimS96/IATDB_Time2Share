<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    /** Owner or admin can update. */
    public function update(User $user, Item $item): bool
    {
        return $user->id === $item->user_id || ($user->is_admin ?? false);
    }

    /** Owner or admin can delete. */
    public function delete(User $user, Item $item): bool
    {
        return $user->id === $item->user_id || ($user->is_admin ?? false);
    }

    /** Any authenticated user can create. */
    public function create(User $user): bool
    {
        return true;
    }
}
