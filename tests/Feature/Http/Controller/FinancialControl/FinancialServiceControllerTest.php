<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\FinancialService;
use App\Models\User;

uses(RefreshDatabase::class);

// pode retornar uma lista com todos os serviços financeiros
test('pode retornar uma lista com todos os serviços financeiros', function (){
    // cria um usuário e um token de acesso
    $user = User::factory(2)->create();
    $token = $user[0]->createToken('Test Token')->plainTextToken;

    // cria 20 serviços financeiros para o usuário
    $financialServiceList = FinancialService::factory(10)->create([
        'user_id' => $user[0]->id,
    ]);

    // solicita a lista com os 10 serviços financeiros
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->get('/api/financial-service');

    $response->assertStatus(200);

    // Converte a resposta JSON em array
    $data = $response->json()['data'];

    // Assert para verificar a quantidade de serviços financeiros retornados
    $this->assertCount(10, $data);

});

