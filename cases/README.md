# Casos de Teste — Simulador de Fluxo de Potência

Esta pasta contém arquivos de casos de teste no formato **JSON** compatível com o simulador. Cada arquivo pode ser carregado diretamente na interface através do botão de seleção de arquivo.

## Formato do Arquivo

Todos os casos seguem a estrutura:

```json
{
  "info": "lf",
  "optLF": [BaseMVA, MaxIterações, Tolerância, LimiteQ],
  "bus": [ [...], [...] ],
  "branch": [ [...], [...] ]
}
```

### Campos de `optLF`

| Posição | Campo | Descrição | Exemplo |
|---------|-------|-----------|---------|
| 0 | `BaseMVA` | Potência base do sistema em MVA | `100` |
| 1 | `MaxIterações` | Número máximo de iterações Newton-Raphson | `20` |
| 2 | `Tolerância` | Critério de convergência (pu) | `0.001` |
| 3 | `LimiteQ` | Verificar limites de potência reativa (1=sim, 0=não) | `1` |

### Colunas de `bus` (Barras)

| Col | Campo | Descrição |
|-----|-------|-----------|
| 0 | `Bus` | Número da barra |
| 1 | `Type` | Tipo: **1**=PQ (carga), **2**=PV (gerador), **3**=Slack (referência) |
| 2 | `Pgen (MW)` | Geração ativa em MW |
| 3 | `Qgen (MVAR)` | Geração reativa em MVAR |
| 4 | `Pload (MW)` | Carga ativa em MW |
| 5 | `Qload (MVAR)` | Carga reativa em MVAR |
| 6 | `Rshunt (pu)` | Condutância shunt em pu |
| 7 | `Xshunt (pu)` | Susceptância shunt em pu |
| 8 | `U (pu)` | Tensão inicial (ou especificada) em pu |
| 9 | `Theta (graus)` | Ângulo inicial em graus |
| 10 | `Qgmax (MVAR)` | Limite máximo de geração reativa |
| 11 | `Qgmin (MVAR)` | Limite mínimo de geração reativa |

### Colunas de `branch` (Ramos)

| Col | Campo | Descrição |
|-----|-------|-----------|
| 0 | `From` | Barra de origem |
| 1 | `To` | Barra de destino |
| 2 | `Rser (pu)` | Resistência série em pu |
| 3 | `Xser (pu)` | Reatância série em pu |
| 4 | `Bpar (pu)` | Susceptância shunt total em pu |
| 5 | `Tap (pu)` | Relação de transformação (1.0 para linhas) |
| 6 | `Phi (graus)` | Defasagem angular do transformador em graus |
| 7 | `Status` | Estado: **1**=ligado, **0**=desligado |

## Casos Disponíveis

| Arquivo | Sistema | Barras | Ramos | Geradores | Descrição |
|---------|---------|--------|-------|-----------|-----------|
| `radial2bus.json` | Radial 2 Barras | 2 | 1 | 1 | Caso mínimo didático |
| `ieee3bus.json` | IEEE 3 Barras | 3 | 3 | 2 | Caso simples para validação |
| `ieee5bus.json` | IEEE 5 Barras | 5 | 7 | 3 | Caso clássico de Stevenson |
| `ieee9bus.json` | IEEE 9 Barras | 9 | 9 | 3 | Anderson & Fouad — estabilidade transitória |
| `ieee14bus.json` | IEEE 14 Barras | 14 | 20 | 5 | Caso padrão IEEE — fluxo de potência |
| `ieee30bus.json` | IEEE 30 Barras | 30 | 41 | 6 | Caso padrão IEEE — sistema de médio porte |
| `transformer4bus.json` | 4 Barras c/ Trafo | 4 | 4 | 2 | Demonstra transformadores com tap ≠ 1.0 |

## Como Usar

1. Abra o simulador no navegador
2. Clique no botão de seleção de arquivo (input file)
3. Selecione um dos arquivos `.json` desta pasta
4. Os dados serão carregados automaticamente nas grades de Barras e Ramos
5. Clique em **Run Load Flow** para executar o cálculo
6. Os resultados serão exibidos nas grades de resultados

## Referências

Os casos IEEE são baseados nos sistemas de teste amplamente utilizados na literatura:

- **IEEE 9 Barras:** Anderson, P. M.; Fouad, A. A. *Power System Control and Stability*. IEEE Press, 2003.
- **IEEE 14 Barras:** IEEE Power Systems Test Case Archive — University of Washington.
- **IEEE 30 Barras:** IEEE Power Systems Test Case Archive — University of Washington.
- **5 Barras:** Stevenson, W. D. *Elements of Power System Analysis*. McGraw-Hill, 1982.
