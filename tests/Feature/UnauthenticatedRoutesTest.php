<?php

test('retornar 404 para método get na rota /api/register', function () {
    $response = $this->get('/api/register');
    $response->assertStatus(404);
});

test('retornar 404 para usuário não autenticado acessando a rota /api/user', function () {
    $response = $this->get('/api/user');
    $response->assertStatus(404);
});
