<?php

    $conn = mysqli_connect("localhost", "root", "1234", "ecommerce");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_cotacao = intval($_POST['id_cotacao']);

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'fornecedor_') === 0) {
                $id_fornecedor = str_replace('fornecedor_', '', $key);
                $vai = $value;

                // Se o fornecedor vai participar, processamos os itens selecionados
                if ($vai === '1') {
                    if (isset($_POST['item_' . $id_fornecedor])) {
                        $itens = $_POST['item_' . $id_fornecedor];

                        foreach ($itens as $id_item) {
                            $id_item = intval($id_item);
                            $id_fornecedor = intval($id_fornecedor);

                            // Verifica se já existe (evita duplicatas)
                            $check = mysqli_query($conn, "
                                SELECT 1 FROM tab_relacao_itens_fornecedores
                                WHERE id_cotacao = $id_cotacao AND id_fornecedor = $id_fornecedor AND id_item = $id_item
                            ");

                            if (mysqli_num_rows($check) === 0) {
                                mysqli_query($conn, "
                                    INSERT INTO tab_relacao_itens_fornecedores (id_cotacao, id_fornecedor, id_item)
                                    VALUES ($id_cotacao, $id_fornecedor, $id_item)
                                ");
                            }
                        }
                    }
                }
            }
        }

        echo 'sucesso';
    } else {
        echo 'erro';
    }
