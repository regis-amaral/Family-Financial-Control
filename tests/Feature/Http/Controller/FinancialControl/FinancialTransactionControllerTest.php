<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\FinancialService;
use App\Models\User;
use App\Models\FinancialTransaction;
use App\Http\Resources\FinancialControl\FinancialTransactionCollection;

uses(RefreshDatabase::class);

it('returns a list with all financial transactions', function () {
    // Cria um usuário
    $user = User::factory()->create();

    // Cria um token de acesso para o usuário
    $token = $user->createToken('Test Token')->plainTextToken;

    // Cria um serviço financeiro para o usuário
    $financialService = FinancialService::factory()->create([
        'user_id' => $user->id,
    ]);

    // Cria algumas transações financeiras associadas ao serviço financeiro
    $financialTransactions = FinancialTransaction::factory(5)->create([
        'financial_service_id' => $financialService->id,
    ]);

    // Envia a solicitação GET para o endpoint de index
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->get('/api/financial/services/' . $financialService->id . '/transactions');

    // Verifica se a resposta está correta
    $response->assertStatus(200);

    // Verifica se a estrutura da resposta está correta
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'date',
                'description',
                'credit',
                'debit',
                'note',
                'financial_service_id',
                'created_at',
                'updated_at',
            ],
        ],
    ]);

    // Verifica se os dados retornados correspondem às transações financeiras criadas
    $response->assertJson([
        'data' => json_decode((new FinancialTransactionCollection($financialTransactions))->toJson(), true),
    ]);

});
