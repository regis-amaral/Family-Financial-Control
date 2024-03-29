<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

// pode efetuar login com sucesso
test('can log in successfully', function (){
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

// pode retornar 422 e mensagem ao não informar o campo email
test('can return 422 and message when not informing the email field', function (){
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

// pode retornar 422 e mensagem ao não informar o campo password
test('can return 422 and message when not entering the password field', function (){
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

// pode retornar 422 e mensagem ao informar o campo email inválido
test('can return 422 and message when entering the invalid email field', function (){
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

// pode retornar 401 e mensagem ao tentar logar com um usuário desativado
test('can return 401 and message when trying to log in with a disabled user', function (){
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

// pode retornar 401 e mensagem ao tentar logar com credenciais incorretas
test('can return 401 and message when trying to log in with incorrect credentials', function (){
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
