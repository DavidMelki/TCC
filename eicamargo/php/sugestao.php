<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($descricao)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Por favor, escreva a sua sugestão.'
        ]);
        exit;
    }

    // =========================================================================
    // FUTURO BANCO DE DADOS:
    // Aqui você fará o INSERT apenas da descrição (e usuário/data se houver).
    // =========================================================================

    echo json_encode([
        'status' => 'success',
        'message' => 'Sugestão cadastrada com sucesso!'
    ]);
    exit;
}

echo json_encode([
    'status' => 'error',
    'message' => 'Método de requisição inválido.'
]);