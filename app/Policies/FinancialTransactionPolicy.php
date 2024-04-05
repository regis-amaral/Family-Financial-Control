<?php

namespace App\Policies;

use App\Models\FinancialService;
use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FinancialTransactionPolicy
{
    /*
     * Determina se o usuário está ativo
     */
    public function before(User $user, string $ability): Response|null
    {
        return $user->active
            ? null
            : Response::denyAsNotFound(__('http.404'), 404);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FinancialTransaction $financialTransaction): Response
    {
        // testa se a transação recebida pertence ao usuário
        return $user->id && $financialTransaction->financial_service->user_id
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
    public function update(User $user, FinancialTransaction $financialTransaction): Response
    {
        // testa se a transação recebida pertence ao usuário
        return $user->id && $financialTransaction->financial_service->user_id
            ? Response::allow()
            : Response::denyAsNotFound(__('http.404'), 404);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FinancialTransaction $financialTransaction): Response
    {
        // testa se a transação recebida pertence ao usuário
        return $user->id && $financialTransaction->financial_service->user_id
            ? Response::allow()
            : Response::denyAsNotFound(__('http.404'), 404);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FinancialTransaction $financialTransaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FinancialTransaction $financialTransaction): bool
    {
        return false;
    }
}
