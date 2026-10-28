<?php
// Consulta de CEP via API pública (ViaCEP)
// Processa o formulário e exibe os resultados dinamicamente

$resultado = null;
$erro = null;
$cep = '';

// Escapa texto para exibição segura em HTML
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

// Retorna o campo da API ou um travessão quando vier vazio
// (CEPs gerais de cidade pequena não têm logradouro nem bairro)
function campo(array $dados, string $chave): string
{
    $valor = trim((string) ($dados[$chave] ?? ''));
    return $valor !== '' ? $valor : '—';
}

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Identifica o tipo de busca: 'cep' (padrão) ou 'endereco'
    $buscaTipo = $_POST['busca_tipo'] ?? 'cep';

    if ($buscaTipo === 'cep' && !empty($_POST['cep'])) {

        // Sanitiza: remove máscara e caracteres não numéricos
        $cep = preg_replace('/\D/', '', $_POST['cep']);

        // Valida se o CEP possui exatamente 8 dígitos numéricos
        if (strlen($cep) !== 8 || !ctype_digit($cep)) {
            $erro = 'Informe um CEP válido com 8 dígitos numéricos.';
        } else {
            // Monta a URL da API pública ViaCEP
            $url = "https://viacep.com.br/ws/{$cep}/json/";

            // Timeout de 5s evita que a página fique travada se a API demorar
            $contexto = stream_context_create([
                'http' => [
                    'method'        => 'GET',
                    'timeout'       => 5,
                    'ignore_errors' => true,
                    'header'        => "Accept: application/json\r\n" .
                                       "User-Agent: Consulta-de-CEP/1.0\r\n",
                ],
            ]);

            // Realiza a requisição HTTP GET para a API
            $conteudo = @file_get_contents($url, false, $contexto);
            $dados = $conteudo !== false ? json_decode($conteudo, true) : null;

            if ($conteudo === false) {
                $erro = 'Não foi possível conectar à API ViaCEP. Tente novamente mais tarde.';
            } elseif (!is_array($dados)) {
                // JSON inválido ou resposta que não é da API (ex.: página de erro)
                $erro = 'A API ViaCEP retornou uma resposta inesperada. Tente novamente.';
            } elseif (isset($dados['erro'])) {
                // A API sinaliza CEP inexistente com {"erro": true}
                $erro = 'CEP não encontrado.';
            } else {
                $resultado = $dados;
            }
        }

    } elseif ($buscaTipo === 'endereco') {

        // Sanitiza os parâmetros da busca por endereço
        $uf = trim(strtoupper((string) ($_POST['uf'] ?? '')));
        $cidade = trim((string) ($_POST['cidade'] ?? ''));
        $logradouro = trim((string) ($_POST['logradouro'] ?? ''));

        if ($uf === '' || $cidade === '' || $logradouro === '') {
            $erro = 'Informe UF, cidade e logradouro para buscar pelo endereço.';
        } else {
            // Monta a URL da API pública ViaCEP para busca por endereço
            // rawurlencode codifica espaços como %20 (mais adequado para caminhos de URL)
            $url = "https://viacep.com.br/ws/{$uf}/" . rawurlencode($cidade) . "/" . rawurlencode($logradouro) . "/json/";

            // Mesmo contexto usado na busca por CEP (funciona no ambiente local)
            $contexto = stream_context_create([
                'http' => [
                    'method'        => 'GET',
                    'timeout'       => 5,
                    'ignore_errors' => true,
                    'header'        => "Accept: application/json\r\n" .
                                       "User-Agent: Consulta-de-CEP/1.0\r\n",
                ],
            ]);

            // Realiza a requisição HTTP GET para a API
            $conteudo = @file_get_contents($url, false, $contexto);
            $dados = $conteudo !== false ? json_decode($conteudo, true) : null;

            if ($conteudo === false) {
                $erro = 'Não foi possível conectar à API ViaCEP. Tente novamente mais tarde.';
            } elseif (!is_array($dados)) {
                // JSON inválido ou resposta que não é da API (ex.: página de erro HTML 404)
                if ($buscaTipo === 'endereco') {
                    $erro = 'Nenhum endereço encontrado para os parâmetros informados.';
                } else {
                    $erro = 'A API ViaCEP retornou uma resposta inesperada. Tente novamente.';
                }
            } elseif (empty($dados)) {
                // Busca por endereço retorna array vazio quando não há resultados
                $erro = 'Nenhum endereço encontrado para os parâmetros informados.';
            } else {
                // Retorna apenas o primeiro resultado (conforme decisão do projeto)
                $resultado = $dados[0];
            }
        }
    }
}

// Reaplica a máscara ao repopular o campo depois do envio
$cepExibicao = strlen($cep) > 5 ? substr($cep, 0, 5) . '-' . substr($cep, 5, 3) : $cep;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de CEP - ViaCEP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <script>
        // Aplica o tema salvo (ou a preferência do sistema) antes de renderizar o conteúdo,
        // para a página não piscar em claro e depois trocar para escuro
        (function () {
            try {
                var salvo = localStorage.getItem('tema');
                var escuro = salvo
                    ? salvo === 'escuro'
                    : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (escuro) document.body.classList.add('escuro');
            } catch (e) {}
        })();
    </script>

    <button type="button" id="toggle-tema" aria-label="Ativar tema escuro" aria-pressed="false">
        <svg class="icone-lua" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"></path>
        </svg>
        <svg class="icone-sol" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="4"></circle>
            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
        </svg>
    </button>

    <header class="header">
        <div class="header-content">
            <h1>Consulta de CEP</h1>
        </div>
        <p class="subtitle">Sistema de consulta de endereço via API pública ViaCEP</p>
    </header>

    <main class="container">
        <div class="tabs" role="tablist" aria-label="Tipo de busca">
            <button type="button" class="tab-btn ativo" data-tab="cep" role="tab" aria-selected="true">Buscar por CEP</button>
            <button type="button" class="tab-btn" data-tab="endereco" role="tab" aria-selected="false">Buscar por Endereço</button>
        </div>

        <form method="POST" action="" class="form-consulta form-cep ativo" id="form-cep">
            <input type="hidden" name="busca_tipo" value="cep">
            <label for="cep">
                <span>CEP:</span>
            </label>
            <input
                type="text"
                id="cep"
                name="cep"
                maxlength="9"
                value="<?= e($cepExibicao) ?>"
                placeholder="00000-000"
                inputmode="numeric"
                autocomplete="postal-code"
                pattern="\d{5}-?\d{3}"
                title="Informe 8 dígitos, no formato 00000-000"
                required
            >
            <button type="submit" id="btn-consultar" data-texto="CONSULTAR">CONSULTAR</button>
        </form>

        <form method="POST" action="" class="form-consulta form-endereco" id="form-endereco">
            <input type="hidden" name="busca_tipo" value="endereco">
            <label for="uf">
                <span>UF:</span>
            </label>
            <select id="uf" name="uf" required>
                <option value="">Selecione o estado</option>
                <option value="AC">Acre (AC)</option>
                <option value="AL">Alagoas (AL)</option>
                <option value="AP">Amapá (AP)</option>
                <option value="AM">Amazonas (AM)</option>
                <option value="BA">Bahia (BA)</option>
                <option value="CE">Ceará (CE)</option>
                <option value="DF">Distrito Federal (DF)</option>
                <option value="ES">Espírito Santo (ES)</option>
                <option value="GO">Goiás (GO)</option>
                <option value="MA">Maranhão (MA)</option>
                <option value="MT">Mato Grosso (MT)</option>
                <option value="MS">Mato Grosso do Sul (MS)</option>
                <option value="MG">Minas Gerais (MG)</option>
                <option value="PA">Pará (PA)</option>
                <option value="PB">Paraíba (PB)</option>
                <option value="PR">Paraná (PR)</option>
                <option value="PE">Pernambuco (PE)</option>
                <option value="PI">Piauí (PI)</option>
                <option value="RJ">Rio de Janeiro (RJ)</option>
                <option value="RN">Rio Grande do Norte (RN)</option>
                <option value="RS">Rio Grande do Sul (RS)</option>
                <option value="RO">Rondônia (RO)</option>
                <option value="RR">Roraima (RR)</option>
                <option value="SC">Santa Catarina (SC)</option>
                <option value="SP">São Paulo (SP)</option>
                <option value="SE">Sergipe (SE)</option>
                <option value="TO">Tocantins (TO)</option>
            </select>
            <label for="cidade">
                <span>Cidade:</span>
            </label>
            <input
                type="text"
                id="cidade"
                name="cidade"
                placeholder="São Paulo"
                required
            >
            <label for="logradouro">
                <span>Logradouro:</span>
            </label>
            <input
                type="text"
                id="logradouro"
                name="logradouro"
                placeholder="Avenida Paulista"
                required
            >
            <button type="submit" id="btn-consultar-endereco" data-texto="CONSULTAR">CONSULTAR</button>
        </form>

        <?php if ($erro): ?>
            <div class="alerta erro" role="alert"><?= e($erro) ?></div>
        <?php endif; ?>

        <?php if ($resultado): ?>
            <div class="resultado" role="status">
                <div class="resultado-header">
                    <h2>Resultado da Consulta</h2>
                    <button type="button" id="btn-copiar" aria-label="Copiar endereço para a área de transferência" title="Copiar endereço">
                        <svg class="icone-clipe" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                        </svg>
                    </button>
                </div>
                <div class="dados-endereco">
                    <div class="item-endereco">
                        <strong>CEP</strong>
                        <span class="cep-valor"><?= e(campo($resultado, 'cep')) ?></span>
                    </div>
                    <div class="item-endereco">
                        <strong>Logradouro</strong>
                        <span><?= e(campo($resultado, 'logradouro')) ?></span>
                    </div>
                    <div class="item-endereco">
                        <strong>Bairro</strong>
                        <span><?= e(campo($resultado, 'bairro')) ?></span>
                    </div>
                    <div class="item-endereco">
                        <strong>Cidade / UF</strong>
                        <span><?= e(campo($resultado, 'localidade') . ' - ' . campo($resultado, 'uf')) ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Máscara automática para o campo de CEP (00000-000)
        const campoCep = document.getElementById('cep');

        campoCep.addEventListener('input', function (e) {
            let valor = e.target.value.replace(/\D/g, ''); // mantém apenas números

            if (valor.length > 5) {
                valor = valor.substring(0, 5) + '-' + valor.substring(5, 8);
            }

            e.target.value = valor;
        });

        // ============================================
        // CONTROLE DO BOTÃO E ESTADO DE CARREGAMENTO
        // ============================================
        const formCep = document.getElementById('form-cep');
        const btnConsultar = document.getElementById('btn-consultar');
        const formEndereco = document.getElementById('form-endereco');
        const btnConsultarEndereco = document.getElementById('btn-consultar-endereco');

        formCep.addEventListener('submit', function () {
            // Desabilita o botão para evitar cliques múltiplos e mostra o spinner
            btnConsultar.disabled = true;
            btnConsultar.classList.add('carregando');
            btnConsultar.textContent = 'CONSULTANDO...';
        });

        formEndereco.addEventListener('submit', function () {
            // Desabilita o botão para evitar cliques múltiplos e mostra o spinner
            btnConsultarEndereco.disabled = true;
            btnConsultarEndereco.classList.add('carregando');
            btnConsultarEndereco.textContent = 'CONSULTANDO...';
        });

        // Ao voltar pelo botão "voltar" do navegador, a página pode vir do cache
        // com o botão ainda travado; este evento restaura o estado normal
        window.addEventListener('pageshow', function () {
            btnConsultar.disabled = false;
            btnConsultar.classList.remove('carregando');
            btnConsultar.textContent = btnConsultar.dataset.texto;

            btnConsultarEndereco.disabled = false;
            btnConsultarEndereco.classList.remove('carregando');
            btnConsultarEndereco.textContent = btnConsultarEndereco.dataset.texto;
        });

        // ============================================
        // TABS: BUSCAR POR CEP | BUSCAR POR ENDEREÇO
        // ============================================
        const tabBtns = document.querySelectorAll('.tab-btn');

        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const tab = btn.dataset.tab;

                // Atualiza estado dos botões de tab
                tabBtns.forEach(function (b) {
                    const ativo = b.dataset.tab === tab;
                    b.classList.toggle('ativo', ativo);
                    b.setAttribute('aria-selected', String(ativo));
                });

                // Mostra/esconde os formulários correspondentes
                formCep.classList.toggle('ativo', tab === 'cep');
                formEndereco.classList.toggle('ativo', tab === 'endereco');
            });
        });

        // ============================================
        // MÁSCARA DE UF (maiúsculas automáticas)
        // ============================================
        const campoUf = document.getElementById('uf');
        if (campoUf) {
            campoUf.addEventListener('input', function (e) {
                e.target.value = e.target.value.toUpperCase();
            });
        }

        // ============================================
        // ALTERNÂNCIA DE TEMA (claro / escuro)
        // ============================================
        const btnTema = document.getElementById('toggle-tema');

        function atualizarBotaoTema() {
            const escuro = document.body.classList.contains('escuro');
            btnTema.setAttribute('aria-pressed', String(escuro));
            btnTema.setAttribute('aria-label', escuro ? 'Ativar tema claro' : 'Ativar tema escuro');
        }

        btnTema.addEventListener('click', function () {
            const escuro = document.body.classList.toggle('escuro');
            try {
                localStorage.setItem('tema', escuro ? 'escuro' : 'claro');
            } catch (e) {}
            atualizarBotaoTema();
        });

        atualizarBotaoTema();

        // ============================================
        // COPIAR ENDEREÇO PARA A ÁREA DE TRANSFERÊNCIA
        // ============================================
        const btnCopiar = document.getElementById('btn-copiar');

        if (btnCopiar) {
            btnCopiar.addEventListener('click', async function () {
                const linhas = [];
                document.querySelectorAll('.resultado .item-endereco').forEach(function (item) {
                    const label = item.querySelector('strong');
                    const valor = item.querySelector('span');
                    if (label && valor) {
                        linhas.push(label.textContent + ': ' + valor.textContent);
                    }
                });

                const texto = linhas.join('\n');

                try {
                    await navigator.clipboard.writeText(texto);
                    btnCopiar.classList.add('copiado');
                    btnCopiar.setAttribute('title', 'Copiado!');
                    setTimeout(function () {
                        btnCopiar.classList.remove('copiado');
                        btnCopiar.setAttribute('title', 'Copiar endereço');
                    }, 2000);
                } catch (err) {
                    console.error('Não foi possível copiar o endereço:', err);
                }
            });
        }
    </script>
</body>
</html>
