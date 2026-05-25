# Web Simulator for Load Flow Calculation in Electrical Systems

## Project Origin

This software is part of the doctoral thesis defended at the Federal University of Uberlândia, Faculty of Electrical Engineering. The full thesis d **doctoral thesis** defended in October 2016 at the **Federal University of Uberlândia (UFU)**, by the **Graduate Program in Electrical Engineering** of the **Faculty of Electrical Engineering (FEELT)**.

> **TAMASHIRO, Márcio Augusto.** *A Web interaction strategy for the analysis of electrical power systems.* 2016. 127 f. Thesis (Doctorate in Sciences) — Federal University of Uberlândia, Uberlândia, 2016.
> DOI: [https://doi.org/10.14393/ufu.te.2016.137](https://doi.org/10.14393/ufu.te.2016.137)

The complete document is available in the UFU Institutional Repository:
**[https://repositorio.ufu.br/handle/123456789/18396](https://repositorio.ufu.br/handle/123456789/18396)**

**Doctoral advisor:** Prof. PhD Geraldo Caixeta Guimarães

**Examination Board:** Prof. Dr Adelio Jose de Moraes (in memoriam), Prof. Dr David Calhau Jorge, Prof. PhD Edgard Afonso Lamounier Júnior, Prof. Dr Sergio Batista da Silva.

---

## Project Description

This software is part of the simulator that implements a **web application for the analysis of electrical power systems**, exploring two central aspects identified as gaps in existing tools: **real-time collaboration** among users and **interoperability** with other applications.

The motivation stems from the realization that commercial programs for electrical systems analysis, although computationally efficient, are not suitable for educational or research purposes because they do not make their source code available. Academic alternatives, usually written in MATLAB, FORTRAN, or C++, are distributed as desktop applications with user-unfriendly interfaces and no remote access features. This work proposes a web approach that combines accessibility, collaboration, and open source code.

## Implemented Features

### Load Flow Analysis

The main module of the application performs the load flow calculation using the **Newton-Raphson method**, allowing:

- Input of bus and branch data directly into editable grids in the interface
- Configuration of execution parameters: base power (MVA), maximum number of iterations, convergence tolerance, and reactive power limit checking
- Loading of test cases from **JSON files** via file selection in the browser (no database server required)
- Display of voltage and angle results per bus, active and reactive power flows per branch, and total system losses
- Indication of the number of iterations until convergence or non-convergence message

### Interface with DHTMLX Grids

Data input and visualization are done through **interactive grids** based on the DHTMLX library, organized into:

| Grid | Content |
|------|---------|
| **OPTIONS** | Base power, maximum iterations, tolerance, and Q limit flag |
| **BUS** | Bus data: type, generation, load, shunt, initial voltage and angle, reactive limits |
| **BRANCH** | Branch data: resistance, reactance, shunt susceptance, tap, and phase shift angle |
| **BUS RESULT** | Results per bus: voltage, angle, generated and consumed powers |
| **BRANCH RESULT** | Results per branch: P and Q flows in both directions and losses |

### Real-Time Collaboration

The application integrates **TogetherJS** (Mozilla), which allows multiple users to work simultaneously on the same case, with real-time synchronization of all changes in the grids (adding/removing buses and branches, editing cells, and loading files).

### Communication with the API

The frontend sends data in **JSON** format via HTTP POST request to the calculation API (`webapi/nws/v1/loadflow`), which executes the Newton-Raphson algorithm on the server and returns the results for display.

## Technologies Used

### Frontend (this repository)
- **HTML5 / CSS3:** Structure and styling of the interface
- **JavaScript:** Client logic and grid manipulation
- **jQuery 2.0.3:** AJAX requests and DOM manipulation
- **DHTMLX:** Interactive grid components (reading, editing, and displaying data)
- **TogetherJS (Mozilla):** Real-time collaboration among users

### Calculation Backend (webapi — in this repository)
- **PHP 8.0+:** REST API implemented with Slim Framework v2, responsible for executing the Newton-Raphson algorithm on the server
- **No database:** all calculations are performed in memory from the data received via JSON

### Data Format
- **JSON:** Single format for data exchange between the frontend, the webapi, and the test case files

## Test Cases

The `cases/` folder contains the JSON files used in the computational tests of the thesis (Appendix A), which served as the basis for validating the results compared to MATPOWER:

| File | System | Buses | Branches | Section in thesis |
|------|--------|-------|----------|-------------------|
| `ieee9bus.json` | IEEE 9 buses | 9 | 9 | A.1 |
| `ieee57bus.json` | IEEE 57 buses | 57 | 78 | A.2 |
| `ieee118bus.json` | IEEE 118 buses | 118 | 186 | A.3 |

All cases adopt the standard parameters from the thesis: 100 MVA base power, 10 maximum iterations, tolerance of 1×10⁻³, and reactive power limit checking active. See `cases/README.md` for a full description of the JSON format.

### Installation

```bash
git clone https://github.com/tamashiroBR/simulador-web-FP.git
```

Open `index.html` directly in the browser or serve the root directory using any web server.

### Usage Flow

1. **Load a case:** Click the file selection field and choose a `.json` from the `cases/` folder
2. **Edit data:** Modify buses and branches directly in the grids, or add/remove rows using the **Add Bus**, **Remove Bus**, **Add Branch**, **Remove Branch** buttons
3. **Run:** Click **Run Load Flow** — the data is sent to the API and the results are displayed in the result grids
4. **Collaborate:** Click **Start TogetherJS** to invite another user and work in real-time on the same case

## References

- TAMASHIRO, M. A. *A Web interaction strategy for the analysis of electrical power systems.* UFU, 2016. Available at: [https://repositorio.ufu.br/handle/123456789/18396](https://repositorio.ufu.br/handle/123456789/18396)
- DHTMLX Documentation: [https://dhtmlx.com/docs/](https://dhtmlx.com/docs/)
- IEEE Power Systems Test Case Archive: [https://labs.ece.uw.edu/pstca/](https://labs.ece.uw.edu/pstca/)
- STEVENSON, W. D. *Elements of Power System Analysis.* McGraw-Hill, 1982.
- ANDERSON, P. M.; FOUAD, A. A. *Power System Control and Stability.* IEEE Press, 2003.
