# Simulador Web para Análise de Sistemas Elétricos

## Descrição do Projeto

O **Simulador Web para Análise de Sistemas Elétricos** é uma aplicação web moderna e robusta desenvolvida em PHP 8 e JavaScript, projetada para permitir a simulação, análise e visualização de sistemas de potência elétricos de forma interativa e intuitiva. Este simulador oferece uma plataforma completa para engenheiros, pesquisadores e estudantes da área de engenharia elétrica realizarem análises complexas de sistemas elétricos através de uma interface web acessível de qualquer navegador.

## Características Principais

### 1. Interface Gráfica Interativa

A aplicação utiliza a biblioteca **DHTMLX** para fornecer uma interface gráfica profissional e responsiva. Os usuários podem interagir com componentes de sistema elétrico através de uma interface intuitiva que suporta:

- **Visualização de Componentes:** Representação visual de geradores, transformadores, linhas de transmissão, cargas e outros elementos de sistemas elétricos.
- **Edição em Tempo Real:** Modificação de parâmetros de componentes sem necessidade de recarregar a página.
- **Múltiplas Visualizações:** Suporte a diferentes modos de visualização (diagrama unifilar, fluxo de potência, etc.).

### 2. Análise de Fluxo de Potência (Load Flow)

Um dos módulos principais do simulador é a análise de fluxo de potência, que permite:

- **Cálculo de Fluxo de Potência:** Determina a distribuição de potência ativa e reativa através das linhas de transmissão.
- **Análise de Tensão:** Calcula as magnitudes e ângulos de fase das tensões em cada barra do sistema.
- **Perdas no Sistema:** Quantifica as perdas de potência ativa e reativa nas linhas de transmissão.
- **Verificação de Limites:** Identifica violações de limites de tensão e carregamento de linhas.

### 3. Análise de Estabilidade Transitória

O simulador também oferece recursos para análise de estabilidade transitória:

- **Simulação Dinâmica:** Simula o comportamento do sistema após distúrbios (como faltas ou desligamentos de linhas).
- **Resposta Temporal:** Mostra a evolução temporal de variáveis do sistema (velocidade de rotores, ângulos, tensões).
- **Critério de Estabilidade:** Avalia se o sistema permanece estável após o distúrbio.
- **Análise de Sensibilidade:** Permite estudar o impacto de diferentes parâmetros na estabilidade do sistema.

### 4. Conectores de Dados Modernos (PHP 8)

O projeto utiliza conectores DHTMLX atualizados para PHP 8, oferecendo:

- **Compatibilidade Total com PHP 8:** Código refatorado que elimina funções depreciadas (`mysql_*`) e utiliza `mysqli` moderno.
- **Prepared Statements:** Implementação de consultas preparadas para máxima segurança contra SQL injection.
- **Type Hints Completos:** Todos os métodos possuem type hints explícitos para melhor segurança de tipo.
- **Tratamento Robusto de Erros:** Exceções bem estruturadas e logging detalhado.

### 5. Arquitetura em Camadas

O projeto segue uma arquitetura bem definida:

```
┌─────────────────────────────────────┐
│     Frontend (HTML/CSS/JavaScript)  │
│  - Interface DHTMLX                 │
│  - Visualização de dados            │
│  - Interação do usuário             │
└──────────────────┬──────────────────┘
                   │
┌──────────────────▼──────────────────┐
│  Backend (PHP 8 / Conectores)       │
│  - Processamento de dados           │
│  - Lógica de simulação              │
│  - Gerenciamento de banco de dados  │
└──────────────────┬──────────────────┘
                   │
┌──────────────────▼──────────────────┐
│  Banco de Dados (MySQL/MariaDB)     │
│  - Armazenamento de projetos        │
│  - Histórico de simulações          │
│  - Parâmetros de componentes        │
└─────────────────────────────────────┘
```

## Tecnologias Utilizadas

### Backend
- **PHP 8.0+:** Linguagem de programação do servidor
- **MySQLi:** Extensão moderna para acesso a banco de dados MySQL
- **DHTMLX Connectors:** Framework para conectar interface com dados

### Frontend
- **HTML5:** Marcação semântica
- **CSS3:** Estilização responsiva
- **JavaScript (ES6+):** Lógica de cliente
- **DHTMLX Library:** Componentes UI profissionais
- **jQuery:** Manipulação do DOM

### Banco de Dados
- **MySQL 5.7+** ou **MariaDB 10.2+**

## Estrutura de Diretórios

```
simulador-web-eletrico/
├── codebase/                          # Biblioteca DHTMLX
│   ├── connector/                     # Conectores de dados (PHP 8)
│   │   ├── base_connector.php         # Classe base
│   │   ├── data_connector.php         # Conector de dados
│   │   ├── db_common.php              # Wrapper de banco de dados
│   │   ├── db_mysqli.php              # Implementação MySQLi
│   │   ├── db_pdo.php                 # Implementação PDO
│   │   ├── dataprocessor.php          # Processador de dados
│   │   └── ...                        # Outros conectores
│   ├── dhtmlx.js                      # Biblioteca JavaScript
│   └── dhtmlx.css                     # Estilos CSS
├── img/                               # Imagens e ícones
├── app.js                             # Aplicação principal (JavaScript)
├── index.html                         # Página principal
├── style.css                          # Estilos customizados
└── README.md                          # Este arquivo
```

## Casos de Uso

### 1. Educação e Pesquisa
Estudantes e pesquisadores podem usar o simulador para:
- Aprender conceitos de análise de sistemas elétricos
- Realizar experimentos numéricos
- Validar teorias e modelos matemáticos
- Publicar resultados de pesquisa

### 2. Planejamento de Sistemas Elétricos
Engenheiros de planejamento podem utilizar para:
- Avaliar a viabilidade de novas linhas de transmissão
- Estudar o impacto de novas gerações (solar, eólica, etc.)
- Analisar cenários de contingência
- Otimizar a operação do sistema

### 3. Operação em Tempo Real
Operadores de sistema podem usar para:
- Simular cenários de operação
- Treinar procedimentos de emergência
- Avaliar limites operacionais
- Planejar manutenção

### 4. Consultoria e Engenharia
Consultores podem utilizar para:
- Estudos de impacto ambiental
- Análise de confiabilidade
- Otimização de custos
- Relatórios técnicos

## Funcionalidades Detalhadas

### Análise de Fluxo de Potência

**O que é:** O fluxo de potência é a distribuição de potência elétrica através das linhas de transmissão de um sistema. Esta análise calcula:

- **Fluxos de Potência Ativa (P):** Potência real transmitida
- **Fluxos de Potência Reativa (Q):** Potência reativa necessária para manutenção de tensão
- **Perdas Técnicas:** Perdas de energia nas linhas e transformadores
- **Perfil de Tensão:** Variação de tensão ao longo do sistema

**Aplicações:**
- Verificar se o sistema pode suprir toda a demanda
- Identificar linhas sobrecarregadas
- Avaliar qualidade de tensão
- Otimizar despacho de geração

### Análise de Estabilidade Transitória

**O que é:** Estuda o comportamento dinâmico do sistema após perturbações, como:

- Faltas (curtos-circuitos)
- Desligamento de linhas
- Perda de geração
- Variações de carga

**Parâmetros Analisados:**
- **Ângulo do Rotor:** Diferença angular entre rotor e campo magnético
- **Velocidade do Rotor:** Desvio da velocidade síncrona
- **Tensão Terminal:** Tensão nos terminais do gerador
- **Corrente de Campo:** Corrente de excitação do gerador

**Critério de Estabilidade:**
Um sistema é considerado estável se, após uma perturbação, ele retorna a um novo ponto de equilíbrio sem oscilações divergentes.

## Requisitos do Sistema

### Mínimos
- PHP 8.0 ou superior
- MySQL 5.7 ou MariaDB 10.2
- 512 MB de RAM
- 100 MB de espaço em disco

### Recomendados
- PHP 8.2 ou superior
- MySQL 8.0 ou MariaDB 10.5+
- 2 GB de RAM
- 500 MB de espaço em disco
- Conexão de internet de alta velocidade

## Instalação

### 1. Clonar o Repositório

```bash
git clone https://github.com/tamashiroBR/simulador-web-eletrico.git
cd simulador-web-eletrico
```

### 2. Configurar o Servidor Web

Configure seu servidor web (Apache, Nginx) para apontar para o diretório do projeto.

**Apache (.htaccess):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.html$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.html [L]
</IfModule>
```

### 3. Configurar Banco de Dados

```bash
# Criar banco de dados
mysql -u root -p < database/schema.sql

# Criar usuário
mysql -u root -p -e "CREATE USER 'simulador'@'localhost' IDENTIFIED BY 'senha_segura';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON simulador.* TO 'simulador'@'localhost';"
```

### 4. Configurar Conexão com Banco de Dados

Editar `codebase/connector/config.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'simulador');
define('DB_PASS', 'senha_segura');
define('DB_NAME', 'simulador');
?>
```

### 5. Acessar a Aplicação

Abrir no navegador: `http://localhost/simulador-web-eletrico/`

## Uso Básico

### Criar um Novo Projeto

1. Clicar em "Novo Projeto"
2. Inserir nome e descrição
3. Selecionar tipo de sistema (radial, malhado, etc.)
4. Clicar em "Criar"

### Adicionar Componentes

1. Selecionar componente na barra de ferramentas
2. Clicar no canvas para posicionar
3. Inserir parâmetros (tensão nominal, potência, etc.)
4. Confirmar

### Executar Simulação

1. Clicar em "Executar Análise"
2. Selecionar tipo de análise (fluxo de potência, transitória)
3. Configurar parâmetros
4. Clicar em "Simular"
5. Visualizar resultados

### Exportar Resultados

1. Clicar em "Exportar"
2. Selecionar formato (PDF, Excel, CSV)
3. Configurar opções
4. Clicar em "Exportar"

## Exemplos de Sistemas

O simulador inclui exemplos pré-configurados:

### 1. Sistema IEEE 14 Barras
- 14 barras
- 5 geradores
- 11 linhas de transmissão
- Usado para testes de algoritmos

### 2. Sistema IEEE 30 Barras
- 30 barras
- 6 geradores
- 41 linhas de transmissão
- Caso de teste padrão

### 3. Sistema Radial Simples
- 5 barras
- 1 gerador
- 4 linhas
- Ideal para aprendizado

## Contribuindo

Contribuições são bem-vindas! Por favor:

1. Fazer fork do projeto
2. Criar uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abrir um Pull Request

## Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo LICENSE para detalhes.

## Autores

- **NDSE Team** - Desenvolvimento inicial
- **Contribuidores** - Veja [CONTRIBUTORS.md](CONTRIBUTORS.md)

## Suporte

Para suporte, abra uma issue no GitHub ou entre em contato através de:
- Email: support@ndse.com
- GitHub Issues: https://github.com/tamashiroBR/simulador-web-eletrico/issues

## Referências Técnicas

- [IEEE Standard 1110 - IEEE Guide for Synchronous Generator Modeling Practices](https://standards.ieee.org/)
- [Power System Dynamics and Stability](https://www.wiley.com/)
- [DHTMLX Documentation](https://dhtmlx.com/docs/)
- [PHP 8 Documentation](https://www.php.net/manual/en/index.php)

## Roadmap

- [ ] Suporte a análise de curto-circuito
- [ ] Análise harmônica
- [ ] Otimização de fluxo de potência (OPF)
- [ ] Integração com SCADA
- [ ] App mobile (React Native)
- [ ] Colaboração em tempo real (WebSocket)
- [ ] Exportação para PowerWorld, PSSE
- [ ] Machine Learning para previsão de carga

## Changelog

### v2.0.0 (Atual)
- Atualização para PHP 8
- Refatoração de conectores de banco de dados
- Melhorias de segurança (prepared statements)
- Type hints completos

### v1.0.0
- Release inicial
- Análise de fluxo de potência
- Análise de estabilidade transitória
- Interface DHTMLX

---

**Desenvolvido com ❤️ para a comunidade de engenharia elétrica**
