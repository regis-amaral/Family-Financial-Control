<?php

// retornar 404 para método get na rota /api/register
test('can return 404 for get method in /api/register route', function () {
    $response = $this->get('/api/register');
    $response->assertStatus(404);
});

// retornar 404 para usuário não autenticado acessando a rota /api/user
test('can return 404 for unauthenticated user accessing the /api/user route', function () {
    $response = $this->get('/api/user');
    $response->assertStatus(404);
});
