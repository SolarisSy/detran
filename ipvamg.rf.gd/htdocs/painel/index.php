<?php
// Iniciar a sessão
session_start();

// Conexão com o banco de dados
$host = 'sql108.infinityfree.com';  // Ou o seu host
$dbname = 'if0_37765204_ipva';  // Nome do banco de dados
$username = 'if0_37765204';  // Seu usuário de banco de dados
$password = 'jTuuwUGHt2ZoK';  // Sua senha do banco de dados

try {
    // Tentar conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obter os dados do formulário
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Consultar o banco de dados para verificar o email e senha
        $sql = "SELECT * FROM usuarios WHERE email = :email AND senha = :senha";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email, ':senha' => $senha]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar se o usuário existe
        if ($user) {
            // Iniciar a sessão e redirecionar para a página protegida
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: alterar.php");  // Redireciona para o painel
            exit;
        } else {
            echo "Usuário ou senha inválidos!";
        }
    }
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css"> <!-- Arquivo CSS -->
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form action="index.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required><br><br>

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
