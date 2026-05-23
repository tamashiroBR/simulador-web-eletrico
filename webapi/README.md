# NDSE Web API — Servidor de Cálculo

Esta pasta contém a **API REST** responsável por executar o cálculo de fluxo de potência e análise de estabilidade transitória. Ela é o backend consumido pelo cliente (`client2`/`simulador-web-eletrico`).

## Arquitetura

```
webapi/
├── index.php              # Ponto de entrada — define as rotas via Slim Framework v2
├── bootstrap.php          # Autoloader PSR-4 para o namespace NDSE
├── .htaccess              # Rewrite rules (Apache) — mapeia /NDSE/webapi/ → index.php
├── Slim/                  # Slim Framework v2.6.1 (incluído localmente)
├── templates/
│   ├── loadflow.php       # Template do endpoint POST /nws/v1/loadflow
│   └── stability.php      # Template do endpoint POST /nws/v1/stability
└── src/NDSE/Core/
    ├── Math/
    │   ├── Complex.php    # Aritmética de números complexos
    │   ├── Matrix.php     # Matriz densa (PHP 8: eval() substituído por match)
    │   ├── Sparse.php     # Matriz esparsa (formato comprimido)
    │   ├── LinAlg.php     # Decomposição LU e resolução de sistemas lineares
    │   └── Angle.php      # Conversão de ângulos
    └── Tools/
        ├── LoadFlow.php   # Algoritmo de Newton-Raphson (sequencial — PHP 8)
        ├── LoadFlowT.php  # Versão com threads (requer pthreads — não usar em PHP 8)
        └── TransientAnalysis.php  # Análise de estabilidade transitória
```

## Endpoints

### `POST /nws/v1/loadflow`

Executa o cálculo de fluxo de potência pelo método de **Newton-Raphson**.

**Corpo da requisição (JSON):**
```json
{
  "info": "lf",
  "optLF": [100, 10, 0.001, 1],
  "bus": [[1, 3, ...], [2, 1, ...], ...],
  "branch": [[1, 2, 0.01, 0.05, ...], ...]
}
```

**Resposta (JSON):**
```json
{
  "iteration": 4,
  "bus": [[1, 1.06, 0.0, 232.4, -16.9, 0, 0, 0, 0], ...],
  "branch": [[1, 2, 156.9, -20.8, -153.6, 27.6, 3.3, 6.8], ...],
  "loss": [13.4, 54.7]
}
```

### `POST /nws/v1/stability`

Executa a análise de estabilidade transitória (requer dados adicionais de geradores, excitatrizes e eventos).

## Requisitos

- **PHP 8.0+** (recomendado PHP 8.1 ou superior)
- **Servidor web Apache** com `mod_rewrite` habilitado
- **Sem banco de dados** — todos os cálculos são realizados em memória

## Instalação

### 1. Posicionar os arquivos

O `.htaccess` e o `index.php` esperam que a API esteja acessível em `/NDSE/webapi/`:

```bash
# Exemplo para Apache com DocumentRoot /var/www/html
cp -r webapi /var/www/html/NDSE/webapi
```

### 2. Habilitar mod_rewrite (Apache)

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Certifique-se de que o `AllowOverride All` está habilitado no VirtualHost:

```apache
<Directory /var/www/html/NDSE/webapi>
    AllowOverride All
</Directory>
```

### 3. Verificar o acesso

```bash
curl http://localhost/NDSE/webapi/
# Resposta esperada: <h1>NDSE Web Simulator web API</h1>
```

### 4. Testar o endpoint de fluxo de potência

```bash
curl -X POST http://localhost/NDSE/webapi/nws/v1/loadflow \
  -H "Content-Type: text/plain" \
  -d '{"info":"lf","optLF":[100,10,0.001,1],"bus":[[1,3,0,0,0,0,0,0,1.04,0,0,0],[2,1,0,0,50,20,0,0,1.0,0,0,0]],"branch":[[1,2,0.05,0.15,0.02,1.0,0,1]]}'
```

## Compatibilidade PHP 8

As seguintes alterações foram realizadas para garantir compatibilidade com PHP 8:

| Arquivo | Problema | Solução |
|---------|----------|---------|
| `templates/loadflow.php` | Usava `LoadFlowT` que depende de `\Thread` (extensão `pthreads`, indisponível no PHP 8 padrão) | Substituído por `LoadFlow` (implementação sequencial equivalente) |
| `src/NDSE/Core/Math/Matrix.php` | Usava `eval()` para avaliar expressões de comparação | Substituído por `match()` com comparações diretas seguras |

> **Nota sobre `LoadFlowT`:** A classe `LoadFlowT` (com threads) foi mantida no repositório como referência histórica. Para utilizá-la em produção, seria necessário instalar a extensão `pthreads` ou `parallel` para PHP 8, o que requer compilação customizada do PHP.

## Configuração do Cliente

O `client2` (`simulador-web-eletrico`) envia requisições para:

```
http://localhost/NDSE/webapi/nws/v1/loadflow
```

Esta URL está definida em `app.js`. Para alterar o endereço do servidor, edite a linha:

```javascript
url: 'http://localhost/NDSE/webapi/nws/v1/loadflow',
```
