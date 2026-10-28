# Consulta de CEP - ViaCEP

Aplicação web em PHP que realiza requisições HTTP para a API pública do [ViaCEP](https://viacep.com.br/), recebe dados em formato JSON, processa-os e apresenta os resultados dinamicamente ao usuário.

## Requisitos

- Servidor web com **PHP 7.4 ou superior** (Apache, Nginx, ou o servidor embutido do PHP)
- Conexão com a internet (para acessar a API ViaCEP)

## Estrutura do projeto

```
consulta-cep/
├── index.php   # Página única: formulário, lógica PHP e exibição de resultados
├── style.css   # Estilos da interface
└── README.md   # Este arquivo
```

## Como executar

### Opção 1: Servidor embutido do PHP

Na raiz do projeto, execute:

```bash
php -S localhost:8000 -t consulta-cep
```

Acesse no navegador:

```
http://localhost:8000
```

### Opção 2: XAMPP / WAMP / Laragon

Copie a pasta `consulta-cep` para a raiz do servidor web (ex.: `htdocs` no XAMPP) e acesse:

```
http://localhost/consulta-cep/
```

## Uso

1. Informe um CEP no campo de pesquisa (ex.: `01001000`).
2. Clique no botão **CONSULTAR**.
3. O PHP fará uma requisição `GET` para:
   ```
   https://viacep.com.br/ws/01001000/json/
   ```
4. Os dados retornados serão exibidos:
   - CEP
   - Logradouro
   - Bairro
   - Cidade
   - Estado

## Funcionalidades

- Página inicial com título do sistema
- Formulário com campo de pesquisa e botão de consulta
- Requisição HTTP GET para API pública ViaCEP
- Interpretação de resposta JSON com `json_decode()`
- Exibição dinâmica dos resultados (CEP, Logradouro, Bairro, Cidade, Estado)
- Tratamento de erros (CEP inválido, não encontrado, falha de conexão)
- Sanitização de entrada e saída para evitar problemas de segurança

## Exemplo de resultado

```
CEP: 01001-000
Logradouro: Praça da Sé
Bairro: Sé
Cidade: São Paulo
Estado: SP
```

## Licença

Livre para fins educacionais e de estudo.