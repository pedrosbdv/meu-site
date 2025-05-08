<?php
$conn = mysqli_connect("localhost", "root", "1234", "ecommerce");
$id_cotacao = 1;

// Busca todos os itens da cotação
$qryBuscaItens = "SELECT * FROM tab_produtos_itens_fornecedores WHERE id_cotacao = $id_cotacao";
$itensCotacao = mysqli_query($conn, $qryBuscaItens);

// Armazena os itens em array para reutilizar
$listaItens = [];
while ($item = mysqli_fetch_object($itensCotacao)) {
    $listaItens[] = $item;
}

// Busca os fornecedores
$qryFornecedores = "SELECT * FROM tab_produtos_fornecedores WHERE id_cotacao = $id_cotacao";
$fornecedores = mysqli_query($conn, $qryFornecedores);

// Montagem do formulário
$results = '<form id="formCotacao"><input type="hidden" name="id_cotacao" value="' . $id_cotacao . '">';

while ($fornecedor = mysqli_fetch_object($fornecedores)) {
    $results .= '
    <div class="card mb-3 mt-3">
        <div class="card-header">
            <strong>' . $fornecedor->nome . '</strong>
            <div class="form-check form-check-inline ml-3">
                <input type="radio" class="aprovado-radio" name="fornecedor_' . $fornecedor->id . '" value="1" checked data-id="' . $fornecedor->id . '">
                <label class="form-check-label"> Aprovado</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" class="aprovado-radio" name="fornecedor_' . $fornecedor->id . '" value="2" data-id="' . $fornecedor->id . '">
                <label class="form-check-label"> Não Aprovado</label>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm" id="tabela_' . $fornecedor->id . '">
                <thead>
                    <tr>
                        <th>Selecionar</th>
                        <th>Item</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($listaItens as $item) {
        $results .= '
                    <tr>
                        <td><input type="checkbox" name="item_' . $fornecedor->id . '[]" value="' . $item->id . '" class="checkbox-item"></td>
                        <td>' . $item->nome_produto . '</td>
                    </tr>';
    }

    $results .= '
                </tbody>
            </table>
        </div>
    </div>';
}

$results .= '<button type="submit" class="btn btn-primary mt-3">Salvar</button></form>';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fornecedores e Itens</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h4>Cotação #<?= $id_cotacao ?></h4>
    <?= $results ?>
</div>

<script>
    $(document).ready(function () {
        $('.aprovado-radio').on('change', function () {
            var id = $(this).data('id');
            var aprovado = $(this).val() === '1';
            $('#tabela_' + id + ' input[type=checkbox]').prop('disabled', !aprovado);
        });

        $('#formCotacao').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: 'salvar.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function (resp) {
                    alert('Salvo com sucesso!');
                    location.reload();
                },
                error: function () {
                    alert('Erro ao salvar!');
                }
            });
        });
    });
</script>
</body>
</html>
