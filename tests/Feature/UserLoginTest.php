<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

test('efetua login com sucesso', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];

    $user = new User();
    $user->fill($user_data);
    $user->save();

    $response = $this->post('/api/login', [
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'token',
                'name'
            ],
            'message'
        ]);

    // Obtenha o conteúdo JSON da resposta
    $responseData = $response->json();

    // Verifique se o token está presente e não está vazio
    $this->assertArrayHasKey('token', $responseData['data']);
    $this->assertNotEmpty($responseData['data']['token']);

    // Verifique se o token é uma string de comprimento razoável
    $this->assertIsString($responseData['data']['token']);
    $this->assertGreaterThanOrEqual(20, strlen($responseData['data']['token']));

//    $response->dump();
});

test('retornar 422 e mensagem ao não informar o campo email', function (){
    $user_data = [
        "password" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/login', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'email' => [__('validation.required',['attribute' => 'email'])]
            ],
        ]);
    //    $response->dump();
});

test('retornar 422 e mensagem ao não informar o campo password', function (){
    $user_data = [
        "email" => "jhon.due@gmail.com",
    ];
    $response = $this->post('/api/login', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'password' => [__('validation.required',['attribute' => 'password'])]
            ],
        ]);
    //    $response->dump();
});

test('retornar 422 e mensagem ao informar o campo email inválido', function (){
    $user_data = [
        "email" => 123,
        "password" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/login', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'email' => [__('validation.email',['attribute' => 'email'])],
            ],
        ]);
//    $response->dump();
});

test('retornar 401 e mensagens ao tentar logar com um usuário desativado', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];
    $user = new User();
    $user->fill($user_data);
    $user->active = false;
    $user->save();

    $response = $this->post('/api/login', $user_data);
    $response->assertStatus(401)
        ->assertJson([
            'data' => __('messages.login.account_inactive'),
        ]);
//    $response->dump();
});

test('retornar 401 e mensagem ao tentar logar com credenciais incorretas', function (){
    $user_data = [
        "email" => "jhon.due@gmail.com",
        "password" => "123",
    ];
    $response = $this->post('/api/login', $user_data);
    $response->assertStatus(401)
        ->assertJson([
            'data' => __('messages.login.invalid_credentials'),
        ]);
//    $response->dump();
});
