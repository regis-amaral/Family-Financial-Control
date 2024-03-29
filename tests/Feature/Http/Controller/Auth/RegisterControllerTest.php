<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

// pode registrar um usuário com sucesso
test('can register a user successfully', function (){
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

// pode retornar 422 e mensagem ao não informar o campo nome
test('can return 422 and message when not informing the name field', function (){
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

// pode retornar 422 e mensagem ao não informar o campo email
test('can return 422 and message when not informing the email field', function (){
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

// pode retornar 422 e mensagem ao não informar o campo password
test('can return 422 and message when not entering the password field', function (){
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

// pode retornar 422 e mensagem ao não informar o campo password_confirmation
test('can return 422 and message when not informing the password_confirmation field', function (){
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

// pode retornar 422 e mensagens ao informar o campo nome com número
test('can return 422 and messages when entering the name field with number', function (){
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

// pode retornar 422 e mensagem ao informar o campo nome com número na string
test('can return 422 and message when entering the name field with number in the string', function (){
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

// pode retornar 422 e mensagem ao informar o campo nome com caracter especial na string
test('can return 422 and message when entering the name field with a special character in the string', function (){
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

// pode retornar 422 e mensagem ao informar o campo email inválido
test('can return 422 and message when entering the invalid email field', function (){
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

// pode retornar 422 e mensagem ao informar a confirmação de senha errada
test('can return 422 and message when entering wrong password confirmation', function (){
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

// pode retornar 422 e mensagem ao informar senha com menos de 8 caracteres
test('can return 422 and message when entering a password with less than 8 characters', function (){
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

// pode retornar 422 e mensagens ao informar senha sem nenhuma letra
test('can return 422 and messages when entering password without any letter', function (){
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

// pode retornar 422 e mensagem ao informar senha sem nenhuma letra maiúscula
test('can return 422 and message when entering password without any capital letters', function (){
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

// pode retornar 422 e mensagem ao informar senha sem nenhum número
test('can return 422 and message when entering password without any number', function (){
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

// pode retornar 422 e mensagem ao informar senha sem nenhum caracter especial
test('can return 422 and message when entering password without any special characters', function (){
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

// pode retornar 422 e mensagem ao informar um email já cadastrado
test('can return 422 and message when entering an email already registered', function (){

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
