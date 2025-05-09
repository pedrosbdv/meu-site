<?php

header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "1234", "ecommerce");

$id_cotacao = isset($_POST['id_cotacao']) ? intval($_POST['id_cotacao']) : 0;
if (!$id_cotacao) {
    echo json_encode([]);
    exit;
}

// Busca os fornecedores aprovados
$fornecedores = [];
foreach ($_POST as $key => $value) {
    if (strpos($key, 'fornecedor_') === 0 && $value == '1') {
        $idFornecedor = str_replace('fornecedor_', '', $key);
        $fornecedores[] = $idFornecedor;
    }
}

if (empty($fornecedores)) {
    echo json_encode([]);
    exit;
}

// Busca os nomes dos fornecedores
$fornecedoresNomes = [];
$queryNomes = "SELECT id, nome FROM tab_produtos_fornecedores WHERE id_cotacao = $id_cotacao";
$resNomes = mysqli_query($conn, $queryNomes);
while ($row = mysqli_fetch_assoc($resNomes)) {
    $fornecedoresNomes[$row['id']] = $row['nome'];
}

// Busca os nomes dos itens
$itensNomes = [];
$queryItens = "SELECT id, nome_produto FROM tab_produtos_itens_fornecedores WHERE id_cotacao = $id_cotacao";
$resItens = mysqli_query($conn, $queryItens);
while ($row = mysqli_fetch_assoc($resItens)) {
    $itensNomes[$row['id']] = $row['nome_produto'];
}

// Monta o JSON de saída
$resultado = [];

foreach ($fornecedores as $idFornecedor) {
    $keyItens = 'item_' . $idFornecedor;
    if (isset($_POST[$keyItens]) && is_array($_POST[$keyItens])) {
        $nomesItens = [];
        foreach ($_POST[$keyItens] as $idItem) {
            if (isset($itensNomes[$idItem])) {
                $nomesItens[] = $itensNomes[$idItem];
            }
        }

        if (!empty($nomesItens)) {
            $resultado[] = [
                'fornecedor' => $fornecedoresNomes[$idFornecedor] ?? 'Fornecedor #' . $idFornecedor,
                'itens' => $nomesItens
            ];
        }
    }
}

echo json_encode($resultado);
