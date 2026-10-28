<?php
// Consulta de CEP via API pública (ViaCEP)
// Processa o formulário e exibe os resultados dinamicamente

$resultado = null;
$erro = null;
$cep = '';

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['cep'])) {

    // Sanitiza: remove máscara e caracteres não numéricos
    $cep = preg_replace('/\D/', '', $_POST['cep']);

    // Valida se o CEP possui exatamente 8 dígitos numéricos
    if (strlen($cep) !== 8 || !ctype_digit($cep)) {
        $erro = 'Informe um CEP válido com 8 dígitos numéricos.';
    } else {
        // Monta a URL da API pública ViaCEP
        $url = "https://viacep.com.br/ws/{$cep}/json/";

        // Realiza a requisição HTTP GET para a API
        $conteudo = @file_get_contents($url);

        if ($conteudo === false) {
            $erro = 'Não foi possível conectar à API ViaCEP. Tente novamente mais tarde.';
        } else {
            // Interpreta o JSON retornado pela API
            $dados = json_decode($conteudo, true);

            // Verifica se a API retornou erro (CEP não encontrado)
            if (isset($dados['erro'])) {
                $erro = 'CEP não encontrado.';
            } else {
                $resultado = $dados;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de CEP - ViaCEP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Consulta de CEP</h1>
        <p class="subtitle">Sistema de consulta de endereço via API pública ViaCEP</p>

        <form method="POST" action="" class="form-consulta">
            <label for="cep">CEP:</label>
            <input
                type="text"
                id="cep"
                name="cep"
                maxlength="9"
                value="<?= htmlspecialchars($cep) ?>"
                placeholder="01001000"
                required
            >
            <button type="submit">CONSULTAR</button>
        </form>

        <?php if ($erro): ?>
            <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if ($resultado): ?>
            <div class="resultado">
                <h2>Resultado da Consulta</h2>
                <p><strong>CEP:</strong> <?= htmlspecialchars($resultado['cep']) ?></p>
                <p><strong>Logradouro:</strong> <?= htmlspecialchars($resultado['logradouro']) ?></p>
                <p><strong>Bairro:</strong> <?= htmlspecialchars($resultado['bairro']) ?></p>
                <p><strong>Cidade:</strong> <?= htmlspecialchars($resultado['localidade']) ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($resultado['uf']) ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>