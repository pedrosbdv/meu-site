<?php
$conn = mysqli_connect("localhost", "root", "1234", "ecommerce");

$id_cotacao = 1;

$qry = "
    SELECT 
        f.id AS id_fornecedor,
        f.nome AS nome_fornecedor,
        i.nome_produto AS nome_item
    FROM 
        tab_relacao_itens_fornecedores r
    JOIN 
        tab_produtos_fornecedores f ON f.id = r.id_fornecedor
    JOIN 
        tab_produtos_itens_fornecedores i ON i.id = r.id_item
    WHERE 
        r.id_cotacao = $id_cotacao
    ORDER BY 
        f.nome, i.nome_produto
";

$result = mysqli_query($conn, $qry);

$dados = [];

while ($row = mysqli_fetch_object($result)) {
    $dados[$row->nome_fornecedor][] = $row->nome_item;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Itens Aprovados por Fornecedor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h4>Itens Aprovados – Cotação #<?= $id_cotacao ?></h4>

    <?php if (!empty($dados)): ?>
        <?php foreach ($dados as $fornecedor => $itens): ?>
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong><?= htmlspecialchars($fornecedor) ?></strong>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <?php foreach ($itens as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning">Nenhum fornecedor com itens aprovados nesta cotação.</div>
    <?php endif; ?>
</div>
</body>
</html>
