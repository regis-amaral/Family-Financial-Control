<?php

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Mockery\MockInterface;

it('pode enviar uma resposta bem-sucedida com código 201', function () {
    // Definindo os parâmetros de entrada
    $result = ['data' => 'result data'];
    $code = 201;
    $message = __('messages.store.success');

    // Criando o mock da resposta
    $responseMock = \Mockery::mock(JsonResponse::class);
    $responseMock->shouldReceive('json')->once()->with([
        'success' => true,
        'data'    => $result,
        'message' => $message,
    ], $code)->andReturnSelf();

    // Substituindo a implementação de Response::json() pelo mock criado
    Response::swap($responseMock);

    // Chamando o método sendResponse da classe BaseController
    $baseController = new BaseController();
    $response = $baseController->sendResponse($result, $code, $message);

    // Verificando se a resposta é o mock esperado
    $this->assertSame($responseMock, $response);
});

it('throws exception', function () {
    $result = ['data' => 'result data'];
    $code = 404;
    $message = __('messages.store.success');
    $baseController = new BaseController();
    $response = $baseController->sendResponse($result, $code, $message);
})->throws(InvalidArgumentException::class, 'Invalid response code.');
