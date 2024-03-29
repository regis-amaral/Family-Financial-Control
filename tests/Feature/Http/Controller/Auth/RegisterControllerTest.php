<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);


test('registrar um usuário com sucesso', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/register', $user_data);

    $response->assertStatus(201)
        ->assertJson([
            'message' => __('messages.store.success'),
        ])
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

// TESTA CAMPOS OBRIGATÓRIOS

test('retornar 422 e mensagem ao não informar o campo nome', function (){
    $user_data = [
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'name' => [__('validation.required',['attribute' => 'name'])]
            ],
        ]);
//        $response->dump();
});

test('retornar 422 e mensagem ao não informar o campo email', function (){
    $user_data = [
        "name" => "Jhon Due",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
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
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password_confirmation" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'password' => [__('validation.required',['attribute' => 'password'])]
            ],
        ]);
    //    $response->dump();
});

test('retornar 422 e mensagem ao não informar o campo password_confirmation', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'password' => [__('validation.confirmed',['attribute' => 'password'])],
            ],
        ]);
//        $response->dump();
});

// TESTA TIPOS DE DADOS INFORMADOS NO CAMPO

test('retornar 422 e mensagens ao informar o campo nome com número', function (){
    $user_data = [
        "name" => 123,
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'name' => [
                    __('validation.string',['attribute' => 'name']),
                    __('validation.regex',['attribute' => 'name']),
                ]
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagem ao informar o campo nome com número na string', function (){
    $user_data = [
        "name" => "Jhon Due 2",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'name' => [__('validation.regex',['attribute' => 'name'])],
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagem ao informar o campo nome com caracter especial na string', function (){
    $user_data = [
        "name" => "Jhon Due ?",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'name' => [__('validation.regex',['attribute' => 'name'])],
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagens ao informar o campo email inválido', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => 123,
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'email' => [__('validation.email',['attribute' => 'email'])],
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagem ao informar a confirmação de senha errada', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "mypersonalp@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'password' => [__('validation.confirmed',['attribute' => 'password'])],
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagem ao informar senha com menos de 8 caracteres', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "P@ss123",
        "password_confirmation" => "P@ss123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'password' => [__('validation.min.string',['attribute' => 'password', 'min' => '8'])]
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagem ao informar senha sem nenhuma letra', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "12345679@",
        "password_confirmation" => "12345679@",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'password' => [
                    __('validation.password.mixed',['attribute' => 'password']),
                    __('validation.password.letters',['attribute' => 'password']),
                ]
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagem ao informar senha sem nenhuma letra maiúscula', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "mypersonalp@assword123",
        "password_confirmation" => "mypersonalp@assword123",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'password' => [
                    __('validation.password.mixed',['attribute' => 'password'])
                ],
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagem ao informar senha sem nenhum número', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword",
        "password_confirmation" => "MyPersonalP@assword",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'password' => [
                    __('validation.password.numbers',['attribute' => 'password'])
                ]
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagem ao informar senha sem nenhum caracter especial', function (){
    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalPassword",
        "password_confirmation" => "MyPersonalPassword",
    ];
    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'password' => [
                    __('validation.password.symbols',['attribute' => 'password'])
                ]
            ],
        ]);
//    $response->dump();
});

test('retornar 422 e mensagem ao informar um email já cadastrado', function (){

    $user_data = [
        "name" => "Jhon Due",
        "email" => "jhon.due@gmail.com",
        "password" => "MyPersonalP@assword123",
        "password_confirmation" => "MyPersonalP@assword123",
    ];

    $user = new User();
    $user->fill($user_data);
    $user->save();

    $response = $this->post('/api/register', $user_data);
    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'email' => [
                    __('validation.unique',['attribute' => 'email'])
                ]
            ],
        ]);
//    $response->dump();
});
