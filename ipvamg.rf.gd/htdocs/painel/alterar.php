<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Chave PIX</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f2f2;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            border-radius: 8px;
        }
        .form-container h3 {
            text-align: center;
        }
        .form-container label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container form-container" style="max-width: 500px;">
    <h3>Alterar Chave PIX</h3>
    <form action="chavepix.php" method="POST">
        <div class="form-group">
            <label for="tipo-chave">Tipo de Chave</label>
            <select class="form-control" id="tipo-chave" name="tipo-chave" required>
                <option value="CNPJ">CNPJ</option>
                <option value="CPF">CPF</option>
                <option value="Celular">Celular</option>
                <option value="Email">Email</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="chave">Chave PIX</label>
            <input type="text" class="form-control" id="chave" name="chave" required placeholder="Digite a chave PIX" />
        </div>

        <button type="submit" class="btn btn-primary btn-block">Alterar Chave PIX</button>
    </form>
</div>

<script>
    // Validação do formato de chave (exemplo básico)
    document.getElementById('tipo-chave').addEventListener('change', function() {
        let tipoChave = this.value;
        let chaveInput = document.getElementById('chave');
        
        if (tipoChave === 'CNPJ') {
            chaveInput.setAttribute('placeholder', 'Digite o CNPJ (11 dígitos)');
        } else if (tipoChave === 'CPF') {
            chaveInput.setAttribute('placeholder', 'Digite o CPF (11 dígitos)');
        } else if (tipoChave === 'Celular') {
            chaveInput.setAttribute('placeholder', 'Digite o número de celular');
        } else if (tipoChave === 'Email') {
            chaveInput.setAttribute('placeholder', 'Digite o e-mail');
        } else if (tipoChave === 'Aleatoria') {
            chaveInput.setAttribute('placeholder', 'Chave Aleatória gerada');
        }
    });
</script>

</body>
</html>
