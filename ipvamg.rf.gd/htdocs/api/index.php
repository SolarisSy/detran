<?php
header('Content-Type: application/json');

// Verifica se o parâmetro 'renavam' foi passado via GET
$renavam = $_GET['renavam'] ?? null;

// Se o parâmetro não foi passado ou estiver vazio
if (!$renavam) {
    echo json_encode([
        "error" => "Renavam não informado"
    ]);
    exit;
}

// URL da API externa
$url = "https://veiculosmg.fazenda.mg.gov.br/api/extrato-debito/renavam/{$renavam}/";

// Inicializa o cURL
$ch = curl_init();

// Configurações do cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

// Executa a requisição
$response = curl_exec($ch);

// Verifica se houve erro na requisição
if(curl_errno($ch)) {
    echo json_encode([
        "error" => "Erro na requisição para a API externa: " . curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

// Fecha a conexão cURL
curl_close($ch);

// Verifica se a resposta é válida
if ($response === false) {
    echo json_encode([
        "error" => "Erro ao obter dados da API externa."
    ]);
    exit;
}

// Converte a resposta em um array associativo
$data = json_decode($response, true);

// Verifica se a resposta contém erro
if (isset($data['error'])) {
    echo json_encode([
        "error" => "Erro ao processar dados da API externa: " . $data['error']
    ]);
    exit;
}

// Extrair dados relevantes para a URL GET
$renavam = $data['renavam'];
$veiculo_tipo = urlencode($data['veiculo']['tipo']);
$veiculo_cor = urlencode($data['veiculo']['cor']);
$veiculo_placa = urlencode($data['veiculo']['placa']);
$veiculo_marca = urlencode($data['veiculo']['marcaModelo']);
$veiculo_ano = $data['veiculo']['anoFabricacao'];
$proprietario_nome = urlencode($data['proprietario']['nome']);
$proprietario_cpf = urlencode($data['proprietario']['cpfCnpj']);
$dt_consulta = urlencode($data['dataHoraConsulta']);

// Iniciar variáveis para os débitos
$debitos = [];
foreach ($data['extratoDebitos'] as $debito) {
    foreach ($debito['parcelas'] as $parcela) {
        $descricao = urlencode($parcela['descricao']);
        $valor_total = $parcela['valorTotal'];
        $data_vencimento = $parcela['dataVencimento'];
        $status_autuacao = urlencode($parcela['statusAutuacao']);
        $esta_pago = $parcela['estaPago'] ? 'sim' : 'não';
        
        // Adiciona cada parcela à lista de débitos
        $debitos[] = "descricao={$descricao}&valor_total={$valor_total}&data_vencimento={$data_vencimento}&status_autuacao={$status_autuacao}&esta_pago={$esta_pago}";
    }
}

// Junta todos os débitos em uma string
$debitos_query = implode('&', $debitos);

// Construir a URL com os dados
// Construir a URL com os dados
$url_redirect = "outra_pagina.php?renavam={$renavam}&veiculo_tipo={$veiculo_tipo}&veiculo_cor={$veiculo_cor}&veiculo_placa={$veiculo_placa}&veiculo_marca={$veiculo_marca}&veiculo_ano={$veiculo_ano}&proprietario_nome={$proprietario_nome}&proprietario_cpf={$proprietario_cpf}&data_consulta={$dt_consulta}&debitos[0][descricao]={$descricao1}&debitos[0][valor_total]={$valor_total1}&debitos[0][data_vencimento]={$data_vencimento1}&debitos[0][status_autuacao]={$status_autuacao1}&debitos[0][esta_pago]={$esta_pago1}&debitos[1][descricao]={$descricao2}&debitos[1][valor_total]={$valor_total2}&debitos[1][data_vencimento]={$data_vencimento2}&debitos[1][status_autuacao]={$status_autuacao2}&debitos[1][esta_pago]={$esta_pago2}";

// Redireciona para a outra página com os dados via GET
header("Location: {$url_redirect}");
exit;
?>
