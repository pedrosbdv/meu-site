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
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>' . $fornecedor->nome . '</strong>
            <label class="radio-inline" style="margin-left: 20px;">
                <input type="radio" class="aprovado-radio" name="fornecedor_' . $fornecedor->id . '" value="1" checked data-id="' . $fornecedor->id . '"> Aprovado
            </label>
            <label class="radio-inline">
                <input type="radio" class="aprovado-radio" name="fornecedor_' . $fornecedor->id . '" value="2" data-id="' . $fornecedor->id . '"> Não Aprovado
            </label>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-condensed" id="tabela_' . $fornecedor->id . '">
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

$results .= '<button type="submit" class="btn btn-primary">Salvar</button></form>';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fornecedores e Itens</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">    
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            const form = $(this);

            $.ajax({
                url: 'gerar_resumo_json.php',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function (dados) {
                    if (dados.length === 0) {
                        Swal.fire('Nenhum item selecionado', 'Selecione ao menos um item para continuar.', 'warning');
                        return;
                    }

                    let html = '';
                    dados.forEach(entry => {
                        html += `<div class="panel panel-default">
                                    <div class="panel-heading"><strong>${entry.fornecedor}</strong></div>
                                    <div class="panel-body"><ul>`;
                        entry.itens.forEach(item => {
                            html += `<li>${item}</li>`;
                        });
                        html += `</ul></div></div>`;
                    });

                    Swal.fire({
                        title: 'Confirmar envio?',
                        html: html,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, salvar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            htmlContainer: 'text-left'
                        },
                        width: '600px'
                    }).then(result => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'salvar.php',
                                type: 'POST',
                                data: form.serialize(),
                                success: function () {
                                    Swal.fire('Salvo com sucesso!', '', 'success').then(() => location.reload());
                                },
                                error: function () {
                                    Swal.fire('Erro ao salvar!', '', 'error');
                                }
                            });
                        }
                    });
                },
                error: function () {
                    Swal.fire('Erro ao gerar resumo!', '', 'error');
                }
            });
        });
    });
</script>
</body>
</html>
