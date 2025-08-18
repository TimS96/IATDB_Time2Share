<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;


class LoanPolicy
{
    public function updateStatus(User $user, Loan $loan, string $action): bool
    {
        $item = $loan->item;
        if (!$item) {
            return false;
        }

        $isOwner    = $item->user_id === $user->id;
        $isBorrower = $loan->borrower_id === $user->id;

        return match ($action) {
            'accept'   => $isOwner      && $loan->status === 'aangevraagd',
            'start'    => $isOwner      && $loan->status === 'geaccepteerd',
            'returned' => $isOwner      && $loan->status === 'actief',
            'reject'   => $isOwner      && $loan->status === 'aangevraagd',
            'cancel'   => $isBorrower   && $loan->status === 'aangevraagd',
            default    => false,
        };
    }
}
