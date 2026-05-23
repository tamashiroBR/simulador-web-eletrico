# Simulador Web para Análise de Sistemas Elétricos de Potência

## Origem do Projeto

Este software é fruto de uma **tese de doutorado** defendida em outubro de 2016 na **Universidade Federal de Uberlândia (UFU)**, pelo **Programa de Pós-graduação em Engenharia Elétrica** da **Faculdade de Engenharia Elétrica (FEELT)**.

> **TAMASHIRO, Márcio Augusto.** *Uma estratégia de interação na Web para a análise de sistemas elétricos de potência.* 2016. 127 f. Tese (Doutorado em Ciências) — Universidade Federal de Uberlândia, Uberlândia, 2016.
> DOI: [https://doi.org/10.14393/ufu.te.2016.137](https://doi.org/10.14393/ufu.te.2016.137)

O documento completo está disponível no Repositório Institucional da UFU:
**[https://repositorio.ufu.br/handle/123456789/18396](https://repositorio.ufu.br/handle/123456789/18396)**

**Orientador:** Prof. Geraldo Caixeta Guimarães

**Banca examinadora:** Prof. Adelio Jose de Moraes, Prof. David Calhau Jorge, Prof. Edgard Afonso Lamounier Júnior, Prof. Sergio Batista da Silva.

---

## Descrição do Projeto

O simulador investiga e implementa uma **aplicação web para análise de sistemas elétricos de potência**, explorando dois aspectos centrais identificados como lacunas nas ferramentas existentes: a **colaboração em tempo real** entre usuários e a **interoperabilidade** com outras aplicações.

A motivação parte da constatação de que os programas comerciais de análise de sistemas elétricos, embora computacionalmente eficientes, não são adequados para fins educacionais ou de pesquisa por não disponibilizarem o código-fonte. As alternativas acadêmicas, em geral escritas em MATLAB, FORTRAN ou C++, são distribuídas como aplicações desktop com interface pouco amigável e sem recursos de acesso remoto. Este trabalho propõe uma abordagem web que combina acessibilidade, colaboração e abertura do código.

## Funcionalidades Implementadas

### Análise de Fluxo de Potência (Load Flow)

O módulo principal da aplicação realiza o cálculo de fluxo de potência pelo **método de Newton-Raphson**, permitindo:

- Entrada de dados de barras e ramos diretamente em grades editáveis na interface
- Configuração de parâmetros de execução: potência base (MVA), número máximo de iterações, tolerância de convergência e verificação de limites de potência reativa
- Carregamento de casos de teste a partir de **arquivos JSON** via seleção de arquivo no navegador (sem necessidade de servidor de banco de dados)
- Exibição dos resultados de tensão e ângulo por barra, fluxos de potência ativa e reativa por ramo, e perdas totais do sistema
- Indicação do número de iterações até a convergência ou mensagem de não convergência

### Interface com Grades DHTMLX

A entrada e visualização de dados é feita por meio de **grades interativas** baseadas na biblioteca DHTMLX, organizadas em:

| Grade | Conteúdo |
|-------|----------|
| **OPTIONS** | Potência base, máximo de iterações, tolerância e flag de limite Q |
| **BUS** | Dados de barras: tipo, geração, carga, shunt, tensão e ângulo iniciais, limites de reativo |
| **BRANCH** | Dados de ramos: resistência, reatância, susceptância shunt, tap e ângulo de defasagem |
| **BUS RESULT** | Resultados por barra: tensão, ângulo, potências geradas e consumidas |
| **BRANCH RESULT** | Resultados por ramo: fluxos de P e Q nos dois sentidos e perdas |

### Colaboração em Tempo Real

A aplicação integra o **TogetherJS** (Mozilla), que permite que múltiplos usuários trabalhem simultaneamente no mesmo caso, com sincronização em tempo real de todas as alterações nas grades (adição/remoção de barras e ramos, edição de células e carregamento de arquivos).

### Comunicação com a API de Cálculo

O frontend envia os dados em formato **JSON** via requisição HTTP POST para a API de cálculo (`webapi/nws/v1/loadflow`), que executa o algoritmo de Newton-Raphson no servidor e retorna os resultados para exibição.

## Tecnologias Utilizadas

### Frontend (este repositório)
- **HTML5 / CSS3:** Estrutura e estilização da interface
- **JavaScript:** Lógica de cliente e manipulação das grades
- **jQuery 2.0.3:** Requisições AJAX e manipulação do DOM
- **DHTMLX:** Componentes de grade interativa (leitura, edição e exibição de dados)
- **TogetherJS (Mozilla):** Colaboração em tempo real entre usuários

### Backend de Cálculo (webapi — neste repositório)
- **PHP 8.0+:** API REST implementada com Slim Framework v2, responsável por executar o algoritmo de Newton-Raphson no servidor
- **Sem banco de dados:** todos os cálculos são realizados em memória a partir dos dados recebidos via JSON

### Formato de Dados
- **JSON:** Formato único para troca de dados entre o frontend, a webapi e os arquivos de casos de teste

## Casos de Teste

A pasta `cases/` contém os arquivos JSON utilizados nos testes computacionais da tese (Apêndice A), que serviram de base para a validação dos resultados em comparação com o MATPOWER:

| Arquivo | Sistema | Barras | Ramos | Seção na tese |
|---------|---------|--------|-------|---------------|
| `ieee9bus.json` | IEEE 9 barras | 9 | 9 | A.1 |
| `ieee57bus.json` | IEEE 57 barras | 57 | 78 | A.2 |
| `ieee118bus.json` | IEEE 118 barras | 118 | 186 | A.3 |

Todos os casos adotam os parâmetros padrão da tese: 100 MVA de potência base, 10 iterações máximas, tolerância de 1×10⁻³ e verificação de limite de potência reativa ativa. Consulte `cases/README.md` para a descrição completa do formato JSON.

### Instalação

```bash
git clone https://github.com/tamashiroBR/simulador-web-eletrico.git
```

Abra `index.html` diretamente no navegador ou sirva o diretório raiz por qualquer servidor web.

### Fluxo de Uso

1. **Carregar um caso:** Clique no campo de seleção de arquivo e escolha um `.json` da pasta `cases/`
2. **Editar dados:** Modifique barras e ramos diretamente nas grades, ou adicione/remova linhas pelos botões **Add Bus**, **Remove Bus**, **Add Branch**, **Remove Branch**
3. **Executar:** Clique em **Run Load Flow** — os dados são enviados à API e os resultados são exibidos nas grades de resultado
4. **Colaborar:** Clique em **Start TogetherJS** para convidar outro usuário e trabalhar em tempo real no mesmo caso

## Referências

- TAMASHIRO, M. A. *Uma estratégia de interação na Web para a análise de sistemas elétricos de potência.* UFU, 2016. Disponível em: [https://repositorio.ufu.br/handle/123456789/18396](https://repositorio.ufu.br/handle/123456789/18396)
- DHTMLX Documentation: [https://dhtmlx.com/docs/](https://dhtmlx.com/docs/)
- IEEE Power Systems Test Case Archive: [https://labs.ece.uw.edu/pstca/](https://labs.ece.uw.edu/pstca/)
- STEVENSON, W. D. *Elements of Power System Analysis.* McGraw-Hill, 1982.
- ANDERSON, P. M.; FOUAD, A. A. *Power System Control and Stability.* IEEE Press, 2003.
