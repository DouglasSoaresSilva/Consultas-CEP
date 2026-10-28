# 📮 Consulta de CEP

> Sistema de consulta de endereço via API pública ViaCEP, desenvolvido como projeto de Programação Web. Consulte qualquer CEP brasileiro e visualize logradouro, bairro, cidade e UF instantaneamente.

## 📋 Descrição

O **Consulta de CEP** é uma aplicação web simples e direta que permite buscar endereços pelo CEP utilizando a API pública do ViaCEP. O usuário digita o CEP no formulário, o sistema consulta a API e exibe os dados do endereço de forma limpa e organizada.

**Disciplina:** Programação Web (PW)  
**Repositório:** [consultas-cep](https://github.com/seu-usuario/consultas-cep)

---

## 🎯 Funcionalidades

✅ **Consulta de CEP**
- Busca endereço completo pelo CEP (formato 00000-000)
- Exibe logradouro, bairro, cidade/UF e CEP
- Integração com a API pública ViaCEP

✅ **Interface Intuitiva**
- Design responsivo (mobile-first)
- Máscara automática de CEP (00000-000) enquanto digita
- Feedback visual de erro (CEP inválido ou não encontrado)
- Mensagem de sucesso com dados do endereço
- Botão de consulta com estado de carregamento

✅ **Tema Claro/Escuro**
- Alternância manual entre temas
- Detecta preferência do sistema automaticamente
- Memoriza a escolha do usuário no navegador

✅ **Validação**
- Valida que o CEP possui exatamente 8 dígitos numéricos
- Máscara e sanitização automática de entrada
- Tratamento de erros de conexão com a API

✅ **Tecnologias**
- Sem banco de dados (consome API externa ViaCEP)
- Sem frameworks (HTML, CSS e JavaScript puro)

---

## 🛠️ Tecnologias Utilizadas

| Camada | Tecnologia |
|--------|-----------|
| **Front-end** | HTML5, CSS3, JavaScript (Vanilla) |
| **Back-end** | PHP 7.4+ |
| **API externa** | ViaCEP (viacep.com.br) |
| **Design** | Flexbox, CSS Variáveis, Google Fonts (Inter), Custom CSS |

---

## 📁 Estrutura do Projeto

```
consultas-cep/
├── index.php              # Página principal (formulário + lógica de consulta)
├── style.css              # Estilos completos (responsivo, tema claro/escuro)
└── README.md              # Este arquivo
```

### Arquivos Principais

**`index.php`**
- Renderiza a página completa com HTML e PHP
- Processa o formulário via POST
- Consome a API ViaCEP usando `file_get_contents` com contexto HTTP
- Sanitiza e valida o CEP (deve ter exatamente 8 dígitos numéricos)
- Reaplica a máscara ao repopular o campo após envio
- Exibe mensagens de erro ou os dados do endereço encontrado

**`style.css`**
- Design moderno, limpo e profissional (CSS puro, sem frameworks)
- Variáveis CSS para cores e espaçamento
- Suporte a tema claro e escuro com transições suaves
- Responsivo: desktop, tablet e mobile
- Fonte: Inter (via Google Fonts)
- Animações de entrada do card e de alertas

---

## 🚀 Como Rodar

### 1️⃣ Colocar no Servidor Local

Copie a pasta `consultas-cep` para o diretório do seu servidor web:

```bash
# Exemplo com XAMPP (Windows)
cp -r consultas-cep C:\xampp\htdocs\

# Ou simplesmente mova a pasta para htdocs
```

### 2️⃣ Rodar Localmente

**Com Apache/PHP (XAMPP, WAMP, etc.):**
```bash
# Abra o navegador e acesse:
http://localhost/consultas-cep
```

**Com PHP embutido:**
```bash
cd C:\xampp\htdocs\consultas-cep
php -S localhost:8000
# Acesse http://localhost:8000
```

### 3️⃣ Testar as Funcionalidades

- 🔍 **Consultar CEP:** Digite um CEP válido (ex: `01001-000`) e clique em "CONSULTAR"
- ✅ **Sucesso:** Os dados do endereço serão exibidos abaixo do formulário
- ❌ **Erro:** CEP inválido ou não encontrado exibe uma mensagem de alerta
- 🌙 **Tema:** Clique no botão de lua/sol no canto superior direito para alternar temas
- 📱 **Mobile:** Acesse pelo celular para ver o layout responsivo

---

## 🎨 Design & UX

### Paleta de Cores (Tema Claro)
- **Azul primário:** `#2563eb` (botão principal, foco)
- **Fundo:** `#f8fafc` (cinza muito claro)
- **Card:** `#ffffff` (branco)
- **Texto:** `#0f172a` (azul escuro quase preto)
- **Texto suave:** `#475569` (cinza médio)
- **Borda:** `#e2e8f0` (cinza claro)
- **Erro:** `#b91c1c` (vermelho)
- **Sucesso:** `#059669` (verde)

### Responsividade
- ✅ Desktop (1200px+)
- ✅ Tablet (até 1024px)
- ✅ Mobile (< 480px)

O tamanho dos elementos (incluindo o título h1) se adapta automaticamente a telas menores.

---

## 🔐 Segurança

- ✅ Sanitização de entrada (`preg_remove` de não numéricos, `htmlspecialchars` na saída)
- ✅ Validação de CEP com exatamente 8 dígitos
- ✅ Timeout de 5s na requisição à API para evitar travamentos
- ✅ Ignora erros de HTTP na requisição externa
- ⚠️ **TODO:** Adicionar validação CSRF (não aplicável a API pública sem sessão)
- ⚠️ **TODO:** Adicionar limitação de requisições (rate limiting)

---

## 📚 Aprendizados & Conceitos

Este projeto aborda:

1. **Back-end PHP**
   - Requisição HTTP com `file_get_contents` e `stream_context_create`
   - Decodificação JSON (`json_decode`)
   - Tratamento de erros de rede e API
   - Sanitização e validação de dados de formulário

2. **Front-end JS/HTML/CSS**
   - Manipulação do DOM (máscara de CEP, estado de carregamento)
   - Event listeners (submit, input, clique)
   - `localStorage` para persistência de tema
   - `matchMedia` para detecção de tema do sistema
   - Layout responsivo com CSS puro

3. **Consumo de API Pública**
   - Requisição GET para ViaCEP
   - Tratamento de resposta JSON
   - Detecção de CEP inexistente (`{"erro": true}`)

4. **UX/UI**
   - Feedback visual (alertas de erro/sucesso)
   - Máscara automática de entrada
   - Tema claro/escuro com detecção de sistema
   - Estado de carregamento no botão

---

## 🐛 Melhorias Futuras

### Não Implementado
- [ ] Histórico de CEPs consultados (localStorage)
- [ ] Botão "Copiar endereço"
- [ ] Integração com mapa (Google Maps/OpenStreetMap)
- [ ] Busca por logradouro (inverso: endereço → CEP)
- [ ] PWA (Progressive Web App) para uso offline
- [ ] Suporte a múltiplos idiomas

### Possíveis Melhorias
- Cache local de consultas recentes
- Paginação ou lista de CEPs favoritos
- Exportar resultado para PDF/texto

---

## 🎥 Vídeo

https://github.com/user-attachments/assets/465a93ee-33b4-4ffb-a45d-283a5c1cc4fc

---

## 👨‍💻 Autor

**Douglas Soares**  
Estudante de Desenvolvimento de Sistemas

---

## 📄 Licença

Este projeto é de uso educacional. Sinta-se livre para estudar, modificar e distribuir.

---

## 🎓 Referências & Recursos

- [ViaCEP - API Pública de CEP](https://viacep.com.br/)
- [Documentação PHP Oficial](https://www.php.net/manual/pt_BR/)
- [MDN Web Docs](https://developer.mozilla.org/pt-BR/)
- [Google Fonts - Inter](https://fonts.google.com/specimen/Inter)
- [W3Schools HTML/CSS/JS](https://www.w3schools.com/)

---

**Última atualização:** 28 de Outubro de 2026  
**Status:** ✅ Funcional (versão 1.0)
