<?php
// Verificando se os dados foram passados via GET
if (isset($_GET['renavam'])) {
    $renavam = $_GET['renavam'];
    $veiculo_tipo = $_GET['veiculo_tipo'];
    $veiculo_cor = $_GET['veiculo_cor'];
    $veiculo_placa = $_GET['veiculo_placa'];
    $veiculo_marca = $_GET['veiculo_marca'];
    $veiculo_ano = $_GET['veiculo_ano'];
    $proprietario_nome = $_GET['proprietario_nome'];
    $proprietario_cpf = $_GET['proprietario_cpf'];
    $data_consulta = $_GET['data_consulta'];
    
    // Exibindo dados do veículo e proprietário
    echo "Renavam: " . $renavam . "<br>";
    echo "Tipo do veículo: " . $veiculo_tipo . "<br>";
    echo "Cor do veículo: " . $veiculo_cor . "<br>";
    echo "Placa: " . $veiculo_placa . "<br>";
    echo "Marca/Modelo: " . $veiculo_marca . "<br>";
    echo "Ano de fabricação: " . $veiculo_ano . "<br>";
    echo "Nome do proprietário: " . $proprietario_nome . "<br>";
    echo "CPF do proprietário: " . $proprietario_cpf . "<br>";
    echo "Data da consulta: " . $data_consulta . "<br><br>";
    
    // Exibindo os débitos
    if (isset($_GET['debitos']) && is_array($_GET['debitos'])) {
        echo "<h3>Débitos:</h3>";
        
        // Itera sobre os débitos passados
        foreach ($_GET['debitos'] as $index => $debito) {
            echo "<strong>Débito " . ($index + 1) . "</strong><br>";
            echo "Descrição: " . htmlspecialchars($debito['descricao']) . "<br>";
            echo "Valor Total: " . htmlspecialchars($debito['valor_total']) . "<br>";
            echo "Data de Vencimento: " . htmlspecialchars($debito['data_vencimento']) . "<br>";
            echo "Status de Autuação: " . htmlspecialchars($debito['status_autuacao']) . "<br>";
            echo "Está Pago: " . htmlspecialchars($debito['esta_pago']) . "<br><br>";
        }
    }
} else {
    echo "Dados não recebidos.";
}
?>
