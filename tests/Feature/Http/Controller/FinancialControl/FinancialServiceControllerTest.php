<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\FinancialService;
use App\Models\User;

uses(RefreshDatabase::class);

// pode retornar uma lista com todos os serviços financeiros
test('pode retornar uma lista com todos os serviços financeiros', function (){
    // cria um usuário e um token de acesso
    $users = User::factory(2)->create();
    $token = $users[0]->createToken('Test Token')->plainTextToken;

    // cria 20 serviços financeiros para o usuário
    $financialServiceList = FinancialService::factory(10)->create([
        'user_id' => $users[0]->id,
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

test('pode criar um novo serviço financeiro para o usuário logado', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Test Token')->plainTextToken;

    $data = [
        "name" => "Banco do Brasil"
    ];
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->post('/api/financial-service', $data);

    $response->assertStatus(201)
        ->assertJson([
            'message' => __('messages.store.success'),
            'data' => [
                'user_id' => $user->id
            ]
        ])
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'user_id'
            ],
            'message'
        ]);

});

test('ao criar um serviço financeiro, retorna erro ao não enviar o campo nome', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Test Token')->plainTextToken;

    $data = [];
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->post('/api/financial-service', $data);

    $response->assertStatus(422)
        ->assertJson([
            'message' => [
                'name' => [__('validation.required',['attribute' => 'name'])]
            ],
        ]);
});

test('ao criar um serviço financeiro, retorna erro não enviar o campo nome com número', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Test Token')->plainTextToken;

    $data = [
        "name" => 123
    ];
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->post('/api/financial-service', $data);

    $response->assertStatus(422)
        ->assertJson([
            'message' => [
                'name' => [
                    __('validation.string',['attribute' => 'name']),
                    __('validation.regex',['attribute' => 'name']),
                ]
            ],
        ]);
//    $response->dump();
});

test('retorna erro 401 ao não enviar um token válido de usuário', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Test Token')->plainTextToken;

    $data = [
        "name" => 123
    ];
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token . '123',
        'Accept' => 'application/json',
    ])->post('/api/financial-service', $data);

    $response->assertStatus(401);
//    $response->dump();
});

test('testa a consulta a um serviço financeiro de um usuário', function (){
    // cria um usuário e um token de acesso
    $users = User::factory(2)->create();
    $tokenUser1 = $users[0]->createToken('Test Token')->plainTextToken;

    // cria 20 serviços financeiros para cada usuário
    $financialServicesUser1 = FinancialService::factory(10)->create([
        'user_id' => $users[0]->id,
    ]);
    $financialServicesUser2 = FinancialService::factory(10)->create([
        'user_id' => $users[1]->id,
    ]);

    // solicita a visualização de um serviço financeiro do usuário 1
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $tokenUser1,
        'Accept' => 'application/json',
    ])->get('/api/financial-service/' . $financialServicesUser1[0]->id);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'user_id' => $users[0]->id
                ],
        ]);

    // solicita a visualização de um serviço financeiro de outro usuário
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $tokenUser1,
        'Accept' => 'application/json',
    ])->get('/api/financial-service/' . $financialServicesUser2[9]->id);

    $response->assertStatus(403)
    ->assertJson([
        "message" => __('http.403')
    ]);

//    $response->dump();
});

test('atualiza um serviço financeiro para o usuário logado', function () {
    // Cria um usuário e um token de acesso
    $user = User::factory()->create();
    $token = $user->createToken('Test Token')->plainTextToken;

    // Cria um serviço financeiro para o usuário
    $financialService = FinancialService::factory()->create([
        'user_id' => $user->id,
        'name' => 'Banco do Brasil'
    ]);

    // Dados para atualização do serviço financeiro
    $dataToUpdate = [
        "name" => "Santander"
    ];

    // Envia a solicitação PUT para a rota de atualização
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->put('/api/financial-service/' . $financialService->id, $dataToUpdate);

    // Verifica se a resposta está OK
    $response->assertStatus(200);

    // Verifica se o nome do serviço financeiro foi atualizado corretamente
    $this->assertDatabaseHas('financial_services', [
        'id' => $financialService->id,
        'name' => 'Santander'
    ]);

    // Verifica se a mensagem de sucesso está presente na resposta
    $response->assertJson([
        'message' => __('messages.update.success')
    ]);

//    $response->dump();
});

test('retorna erro 403 ao tentar atualizar um serviço financeiro de outro usuário', function () {
    // Cria um usuário e um token de acesso
    $users = User::factory(2)->create();
    $tokenUser2 = $users[1]->createToken('Test Token')->plainTextToken;
    // Cria um serviço financeiro para o usuário
    $financialService = FinancialService::factory()->create([
        'user_id' => $users[0]->id,
        'name' => 'Banco do Brasil'
    ]);

    // Dados para atualização do serviço financeiro
    $dataToUpdate = [
        "name" => "Santander"
    ];

    // Envia a solicitação PUT para a rota de atualização
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $tokenUser2,
        'Accept' => 'application/json',
    ])->put('/api/financial-service/' . $financialService->id, $dataToUpdate);

    $response->assertStatus(403)
        ->assertJson([
            "message" => __('http.403')
        ]);

//    $response->dump();
});

test('testa as validações ao atualizar um serviço financeiro', function () {
    // Cria um usuário e um token de acesso
    $user = User::factory()->create();
    $token = $user->createToken('Test Token')->plainTextToken;

    // Cria um serviço financeiro para o usuário
    $financialService = FinancialService::factory()->create([
        'user_id' => $user->id,
        'name' => 'Banco do Brasil'
    ]);

    // Dados para atualização do serviço financeiro
    $dataToUpdate = [];

    // Envia a solicitação PUT para a rota de atualização
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->put('/api/financial-service/' . $financialService->id, $dataToUpdate);

    $response->assertStatus(422)
        ->assertJson([
            'message' => [
                'name' => [__('validation.required',['attribute' => 'name'])]
            ],
        ]);

    // Dados para atualização do serviço financeiro
    $dataToUpdate = [
        "name" => 123
    ];

    // Envia a solicitação PUT para a rota de atualização
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->put('/api/financial-service/' . $financialService->id, $dataToUpdate);

    $response->assertStatus(422)
        ->assertJson([
            'message' => [
                'name' => [
                    __('validation.string',['attribute' => 'name']),
                    __('validation.regex',['attribute' => 'name']),
                ]
            ],
        ]);

//    $response->dump();
});

test('remove um serviço financeiro pertencente ao usuário logado', function () {
    // Cria um usuário e um token de acesso
    $user = User::factory()->create();
    $token = $user->createToken('Test Token')->plainTextToken;

    // Cria um serviço financeiro para o usuário
    $financialService = FinancialService::factory()->create([
        'user_id' => $user->id,
    ]);

    // Envia a solicitação DELETE para a rota de exclusão
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->delete('/api/financial-service/' . $financialService->id);

    // Verifica se a resposta está OK
    $response->assertStatus(200);

    // Verifica se o serviço financeiro foi removido do banco de dados
    $this->assertDatabaseMissing('financial_services', [
        'id' => $financialService->id,
    ]);

    // Verifica se a mensagem de sucesso está presente na resposta
    $response->assertJson([
        'message' => __('messages.destroy.success')
    ]);
});

test('tenta remover um serviço financeiro de outro usuário', function () {
    // Cria dois usuários
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Cria um serviço financeiro pertencente ao segundo usuário
    $financialService = FinancialService::factory()->create([
        'user_id' => $user2->id,
    ]);

    // Cria um token de acesso para o primeiro usuário
    $token = $user1->createToken('Test Token')->plainTextToken;

    // Envia a solicitação DELETE para a rota de exclusão, tentando remover o serviço financeiro do segundo usuário
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->delete('/api/financial-service/' . $financialService->id);

    // Verifica se a resposta está correta (espera-se uma resposta 403 Proibido)
    $response->assertStatus(403);

    // Verifica se o serviço financeiro ainda está presente no banco de dados
    $this->assertDatabaseHas('financial_services', [
        'id' => $financialService->id,
    ]);

    // Verifica se a mensagem de erro está presente na resposta JSON
    $response->assertJson([
        'message' => __('http.403')
    ]);
});
