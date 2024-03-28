<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);


test('an action that requires authentication', function () {
    $user = User::factory()->create();
    // Gerar um token Sanctum para o usuário
    $token = $user->createToken('Test Token')->plainTextToken;
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json', // Defina o tipo de conteúdo aceito como JSON
    ])->get('/api/user');
    $response->assertStatus(200);
});

