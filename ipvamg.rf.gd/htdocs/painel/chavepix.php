<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está autenticado (exemplo básico)
if (!isset($_SESSION['user_id'])) {
    die("Acesso não autorizado!");
}

// Conexão com o banco de dados
$host = 'sql108.infinityfree.com';  // Ou o seu host
$dbname = 'if0_37765204_ipva';  // Nome do banco de dados
$username = 'if0_37765204';  // Seu usuário de banco de dados
$password = 'jTuuwUGHt2ZoK';  // Sua senha do banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Obter os dados do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipoChave = $_POST['tipo-chave'];
    $chave = $_POST['chave'];
    $userId = $_SESSION['user_id']; // Supondo que o ID do usuário esteja na sessão

    // Validar o tipo de chave
    $erro = '';
    if ($tipoChave == 'CNPJ') {
        // Validação básica de CNPJ (apenas números, 14 dígitos)
        if (!preg_match('/^\d{14}$/', $chave)) {
            $erro = "CNPJ inválido!";
        }
    } elseif ($tipoChave == 'CPF') {
        // Validação básica de CPF (apenas números, 11 dígitos)
        if (!preg_match('/^\d{11}$/', $chave)) {
            $erro = "CPF inválido!";
        }
    } elseif ($tipoChave == 'Celular') {
        // Validação básica de celular (apenas números, 11 dígitos)
        if (!preg_match('/^\d{11}$/', $chave)) {
            $erro = "Número de celular inválido!";
        }
    } elseif ($tipoChave == 'Email') {
        // Validação básica de email
        if (!filter_var($chave, FILTER_VALIDATE_EMAIL)) {
            $erro = "Email inválido!";
        }
    } elseif ($tipoChave == 'Aleatoria') {
        // Chave aleatória pode ser qualquer string
        $chave = bin2hex(random_bytes(8));  // Gera uma chave aleatória de 16 caracteres
    }

    // Se houve erro na validação
    if ($erro != '') {
        echo $erro;
        exit;
    }

    // Atualizar a chave PIX no banco de dados
    try {
        $sql = "UPDATE usuarios SET chave_pix = :chave, tipo_chave = :tipo_chave WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':chave' => $chave,
            ':tipo_chave' => $tipoChave,
            ':user_id' => $userId
        ]);

        echo "Chave PIX alterada com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao atualizar chave PIX: " . $e->getMessage();
    }
} else {
    echo "Método inválido!";
}
?>
