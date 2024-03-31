<?php

use App\Models\User;

// com um token valido, pode retornar informações do usuário logado na rota /api/user
test('with a valid token, can return information about the user logged in via the /api/user route', function (){
    $user = User::factory()->create();
    // Gerar um token Sanctum para o usuário
    $token = $user->createToken('Test Token')->plainTextToken;
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->get('/api/user');
    $response->assertStatus(200);
});

// com um token inválido, retorna 401 code na rota /api/user
test('with an invalid token, returns 401 code on the /api/user route', function (){
    $user = User::factory()->create();
    // Gerar um token Sanctum para o usuário
    $token = "dlashdoasdsa98sdahlkfhaslkdjgf";
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->get('/api/user');
    $response->assertStatus(401);
});

//
test('returns a 404 code to the route /', function () {
    $response = $this->get('/');
    $response->assertStatus(404);
});

test('returns a 404 code for unauthenticated user in the /api/user route', function () {
    $response = $this->get('/api/user');
    $response->assertStatus(404);
});
