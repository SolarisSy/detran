<?php
// Sanitize and set default values for GET parameters
$renavam = isset($_GET['renavam']) ? htmlspecialchars($_GET['renavam']) : '';
$veiculo_tipo = isset($_GET['veiculo_tipo']) ? htmlspecialchars($_GET['veiculo_tipo']) : '';
$veiculo_cor = isset($_GET['veiculo_cor']) ? htmlspecialchars($_GET['veiculo_cor']) : '';
$veiculo_placa = isset($_GET['veiculo_placa']) ? htmlspecialchars($_GET['veiculo_placa']) : '';
$veiculo_marca = isset($_GET['veiculo_marca']) ? htmlspecialchars($_GET['veiculo_marca']) : '';
$veiculo_ano = isset($_GET['veiculo_ano']) ? htmlspecialchars($_GET['veiculo_ano']) : '';
$proprietario_nome = isset($_GET['proprietario_nome']) ? htmlspecialchars($_GET['proprietario_nome']) : '';
$proprietario_cpf = isset($_GET['proprietario_cpf']) ? htmlspecialchars($_GET['proprietario_cpf']) : '';
$data_consulta = isset($_GET['data_consulta']) ? htmlspecialchars($_GET['data_consulta']) : '';
$anoExercicio = $_GET['anoExercicio'] ?? null;
// Verifique se a variável $debitos está definida
$debitos = isset($_GET['debitos']) ? $_GET['debitos'] : [];

// Agrupar débitos por ano
$debitosPorAno = [];
foreach ($debitos as $debito) {
    foreach ($debito['parcelas'] as $parcela) {
        $ano = substr($parcela['data_vencimento'], 6, 4); // Extrai o ano da data
        if (!isset($debitosPorAno[$ano])) {
            $debitosPorAno[$ano] = [];
        }
        $debitosPorAno[$ano][] = $parcela;
    }
}

// Adicionar os débitos de 2024 também para 2025
if (isset($debitosPorAno['2024'])) {
    $debitosPorAno['2025'] = $debitosPorAno['2024']; // Duplicando o ano de 2024 para 2025
}

// Filtra apenas o ano de 2025
$debitosPorAno = isset($debitosPorAno['2025']) ? ['2025' => $debitosPorAno['2025']] : [];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Débitos do Veículo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: #f2f2f2;
        }
        .custom-container {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #ffffff52;
        }
        .logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logos > a {
            display: flex;
            flex-direction: column;
        }
        .logos > a > :first-child {
            color: #0a4a22;
            font-size: 19px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .logos > a > :last-child {
            font-size: 19px;
            color: rgb(17, 17, 17);
        }
        .details {
            margin-top: 20px;
        }
        .divider {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
        .vehicle-data li {
            list-style-type: none;
            margin-bottom: 5px;
            padding-left: 0;
            display: inline-block;
            margin-right: 20px;
        }
        .vehicle-data li i {
            margin-right: 5px;
        }
        .custom-checkbox {
            width: 20px;
            height: 20px;
            margin: 0;
        }
        .custom-checkbox:checked {
            background-color: #007bff;
            border-color: #007bff;
        }
        .custom-checkbox:checked:after {
            content: '\2713';
            color: white;
            font-size: 15px;
            position: absolute;
            top: 2px;
            left: 6px;
        }
        .select-th {
            width: 30px;
        }
        .subtotal-total {
            margin-top: 20px;
        }
        .pay-via-pix {
            margin-top: 10px;
            background-color: white;
            color: black;
            border: 2px solid black;
            font-weight: bold;
        }
        .pay-via-pix:hover {
            background-color: #f0f0f0; /* Cor de fundo suave ao passar o mouse */
            border-color: #333; /* Contorno mais escuro quando o mouse passa */
        }
		.desconto {
    font-size: 12px;
}
.ultimo-dia {
    background-color: red;      /* Cor de fundo vermelha */
    color: white;               /* Cor do texto branca */
    padding: 3px 10px;          /* Espaçamento interno (padding) */
    font-size: 10px;            /* Tamanho da fonte */
    font-weight: bold;          /* Deixar o texto em negrito */
    border-radius: 5px;         /* Bordas arredondadas */
    display: inline-block;      /* Exibe como um bloco inline */
    text-align: center;         /* Alinha o texto ao centro */
    margin-top: 10px;           /* Espaço superior (ajuste conforme necessário) */
}

    </style>
</head>
<body>
<div class="container mt-3 custom-container">
    <div class="logos">
        <img src="detran-mg.png" style="max-width: 410px;" alt="">
    </div>
    <div class="mt-3 vehicle-data">
        <h5 style="font-size: 18px; background: #e2e2e2; border-top-left-radius: 6px; border-top-right-radius: 6px; padding: 10px; color: #000; margin-bottom: 0;"><i class="fas fa-list"></i> Detalhes do Veículo</h5>
        <ul class="pl-0" style="padding: 10px !important; border: solid 1px #000; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px; display: flex; flex-direction: column;">
            <li><i class="fa-solid fa-id-card"></i> <strong>CPF / CNPJ:</strong> <?php echo htmlspecialchars($proprietario_cpf); ?></li>
            <li><i class="fa-solid fa-id-card"></i> <strong>Nome Proprietário:</strong> <?php echo htmlspecialchars($proprietario_nome); ?></li>
            <li><i class="fas fa-car"></i> <strong>RENAVAM:</strong> <?php echo htmlspecialchars($renavam); ?></li>
            <li><i class="fas fa-car"></i> <strong>Placa:</strong> <?php echo htmlspecialchars($veiculo_placa); ?></li>
            <li><i class="fas fa-car"></i> <strong>Tipo de Veículo:</strong> <?php echo htmlspecialchars($veiculo_tipo); ?></li>
            <li><i class="fas fa-car"></i> <strong>Cor:</strong> <?php echo htmlspecialchars($veiculo_cor); ?></li>
            <li><i class="fas fa-car"></i> <strong>Marca:</strong> <?php echo htmlspecialchars($veiculo_marca); ?></li>
            <li><i class="fas fa-car"></i> <strong>Ano:</strong> <?php echo htmlspecialchars($veiculo_ano); ?></li>
        </ul>
    </div>
    <div class="mt-3">
        <h5><i class="fas fa-credit-card"></i> Débitos Do Veiculo </h5>
        <?php
        // Exibe os débitos apenas para o ano de 2025
        if (isset($debitosPorAno['2025'])) {
            $parcelas = $debitosPorAno['2025'];
            ?>
			                        <?php
                        $totalAno = 0; // Variável para armazenar o total do ano
                        foreach ($parcelas as $parcela) {
                            ?>
							                            <?php
                            $totalAno += $parcela['valor_total'];
                        }
                        ?>
            <div class="year-row" style="cursor: pointer; background: #f90000; padding: 10px; margin: 5px 0; border-radius: 5px;">
                <strong>TRIBUTO/PARCELA RECEITA  DESCONTOS</strong>
            </div>

            <div class="debt-details" id="debt-details-2025" style="display: block;"> <!-- Mudança para "display: block" -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Seleção</th><br>
                            <th>Descrição</th>
                            <th>Valor</th>
                        </tr>
						<tr>
						<td><input type="checkbox" class="debitoCheckbox" value="<?php echo $totalAno += $parcela['valor_total']; ?>" onclick="updateTotal()"></td>
                                <td>IPVA <span class="desconto"></span><?php echo htmlspecialchars($anoExercicio); ?> - COTA ÚNICA <div class="ultimo-dia">ÚLTIMO DIA</div><br><span class="desconto">10% DE DESCONTO - PAGAMENTO ADIANTADO</span></td>
								<td><strong></strong> R$ <?php echo number_format($totalAno, 2, ',', '.'); ?></td></thead>
                        </tr>
					<tbody>
                        <?php
                        $totalAno = 0; // Variável para armazenar o total do ano
                        foreach ($parcelas as $parcela) {
                            ?>
							
                            <tr>
                                <td><input type="checkbox" class="debitoCheckbox" value="<?php echo $parcela['valor_total']; ?>" onclick="updateTotal()"></td>
                                <td><?php echo htmlspecialchars($parcela['descricao']); ?><span>ª PARCELA</span> <?php echo htmlspecialchars($anoExercicio); ?><br><span class="desconto">5% DE DESCONTO - PARCELAMENTO</span></td>
                                <td>R$ <?php echo number_format($parcela['valor_total'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php
                            $totalAno += $parcela['valor_total'];
                        }
                        ?>
                    </tbody>
                </table>
		<h5 style="font-size: 18px; background: #e2e2e2; border-top-left-radius: 6px; border-top-right-radius: 6px; padding: 10px; color: #000; margin-bottom: 0;"><i class="fas fa-list"></i> Pagamento</h5>
                <div>
                    <strong>Subtotal: </strong> R$ <?php echo number_format($totalAno, 2, ',', '.'); ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
	
    <div class="subtotal-total">
        <h5>Total: <span id="total">R$ 0,00</span></h5>
        <button class="btn btn-primary pay-via-pix" id="payButton" disabled style="background-color: white; color: black; border: 2px solid black;">Pagar via PIX <span style="color: #ff0000;">❖</span></button>
    </div>
</div>

<form id="paymentForm" action="pagar.php" method="GET" style="display: none;">
    <input type="hidden" name="renavam" id="hiddenRenavam">
    <input type="hidden" name="veiculo_tipo" id="hiddenVeiculoTipo">
    <input type="hidden" name="veiculo_cor" id="hiddenVeiculoCor">
    <input type="hidden" name="veiculo_placa" id="hiddenVeiculoPlaca">
    <input type="hidden" name="veiculo_marca" id="hiddenVeiculoMarca">
    <input type="hidden" name="veiculo_ano" id="hiddenVeiculoAno">
    <input type="hidden" name="proprietario_nome" id="hiddenProprietarioNome">
    <input type="hidden" name="proprietario_cpf" id="hiddenProprietarioCpf">
    <input type="hidden" name="data_consulta" id="hiddenDataConsulta">
    <input type="hidden" name="total" id="hiddenTotal">
</form>

<script>
// Atualizar o total com um possível desconto de 10% no pagamento via PIX
function updateTotal() {
    let total = 0;
    const checkboxes = document.querySelectorAll('.debitoCheckbox:checked');
    checkboxes.forEach(function(checkbox) {
        total += parseFloat(checkbox.value);
    });

    // Aplica o desconto de 10% para pagamento via PIX
    const desconto = total * 0.10; // 10% de desconto
    const totalComDesconto = total - desconto;

    // Exibe o total com desconto
    document.getElementById('total').textContent = 'R$ ' + totalComDesconto.toFixed(2).replace('.', ',');

    // Habilitar o botão de pagamento se algum débito for selecionado
    document.getElementById('payButton').disabled = checkboxes.length === 0;
}

// Selecionar todos os checkboxes de um ano
function toggleSelectYear(ano) {
    const isChecked = document.querySelector('#debt-details-' + ano + ' .selectAllYear').checked;
    const checkboxes = document.querySelectorAll('#debt-details-' + ano + ' .debitoCheckbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = isChecked;
    });
    updateTotal();
}

// Submeter o formulário com os dados via GET para a página de pagamento
document.getElementById('payButton').addEventListener('click', function() {
    const selectedDebitos = [];
    document.querySelectorAll('.debitoCheckbox:checked').forEach(function(checkbox) {
        selectedDebitos.push(checkbox.value);
    });

    // Preenche os campos escondidos do formulário com os dados necessários
    document.getElementById('hiddenRenavam').value = '<?php echo $renavam; ?>';
    document.getElementById('hiddenVeiculoTipo').value = '<?php echo $veiculo_tipo; ?>';
    document.getElementById('hiddenVeiculoCor').value = '<?php echo $veiculo_cor; ?>';
    document.getElementById('hiddenVeiculoPlaca').value = '<?php echo $veiculo_placa; ?>';
    document.getElementById('hiddenVeiculoMarca').value = '<?php echo $veiculo_marca; ?>';
    document.getElementById('hiddenVeiculoAno').value = '<?php echo $veiculo_ano; ?>';
    document.getElementById('hiddenProprietarioNome').value = '<?php echo $proprietario_nome; ?>';
    document.getElementById('hiddenProprietarioCpf').value = '<?php echo $proprietario_cpf; ?>';
    document.getElementById('hiddenDataConsulta').value = '<?php echo $data_consulta; ?>';
    document.getElementById('hiddenTotal').value = document.getElementById('total').textContent;

    // Envia o formulário de pagamento
    document.getElementById('paymentForm').submit();
});
</script>
</body>
</html>
