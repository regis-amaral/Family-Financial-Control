<?php

test('returns a 404 code to the route /', function () {
    $response = $this->get('/');
    $response->assertStatus(404);
});

test('returns a 404 code for unauthenticated user in the /api/user route', function () {
    $response = $this->get('/api/user');
    $response->assertStatus(404);
});
