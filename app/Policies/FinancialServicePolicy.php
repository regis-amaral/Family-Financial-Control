<?php

namespace App\Policies;

use App\Models\FinancialService;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FinancialServicePolicy
{
    /*
     * Determina se o usuário está ativo
     */
    public function before(User $user, string $ability): Response|null
    {
        return $user->active
            ? null
            : Response::deny(__('http.403'));
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FinancialService $financialService): Response
    {
        return $user->id === $financialService->user_id
            ? Response::allow()
            : Response::denyAsNotFound(__('http.404'), 404);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FinancialService $financialService): Response
    {
        return $user->id === $financialService->user_id
            ? Response::allow()
            : Response::denyAsNotFound(__('http.404'), 404);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FinancialService $financialService): Response
    {
        return $user->id === $financialService->user_id
            ? Response::allow()
            : Response::denyAsNotFound(__('http.404'), 404);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FinancialService $financialService): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FinancialService $financialService): bool
    {
        return false;
    }
}
