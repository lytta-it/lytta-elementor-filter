# Lytta Elementor Advanced Filter

Un plugin professionale esclusivo, sviluppato da **[Lytta.it](https://lytta.it)**, per integrare un sistema di filtraggio avanzato, scalabile e dal design eccezionale direttamente all'interno di **Elementor Pro**.

![Versione](https://img.shields.io/badge/Versione-1.1.0-blue.svg)
![Elementor Pro Compatibile](https://img.shields.io/badge/Elementor%20Pro-✓-success.svg)
![ACF Compatibile](https://img.shields.io/badge/ACF-✓-success.svg)

## 🚀 Caratteristiche Principali

Il plugin è progettato per essere estremamente leggero (impatto zero sui Web Vitals) e per disaccoppiare la logica di ricerca dal rendering visivo (affidato interamente al Loop Grid nativo di Elementor).

*   **100% Nativo per Elementor Pro:** Si connette istantaneamente a qualsiasi *Loop Grid* o *Posts Widget* tramite `Query ID`.
*   **Selettori Dinamici:** Niente più "slug" manuali. Il plugin popola automaticamente i menu a tendina nell'editor di Elementor leggendo dal tuo database:
    *   Tutte le Tassonomie pubbliche (Categorie, Tag, ecc.)
    *   Tutti i Campi ACF (Advanced Custom Fields)
*   **Integrazione Intelligente ACF:** Estrae le opzioni (choices) dai campi `Select` di ACF. Se non esistono opzioni predefinite, effettua una scansione automatica per estrarre i meta-valori distinti realmente usati nei post pubblicati.
*   **Interfaccia Utente "Pro":**
    *   **Etichette separate:** Controllo granulare tra la Label esterna e il Placeholder interno degli input.
    *   **Active Chips (Novità v1.1):** Mostra delle eleganti etichette cliccabili sopra ai risultati per rimuovere singolarmente i filtri attivi.
    *   **Auto-Submit & Reset:** Ricarica istantanea dei risultati in base ai parametri URL (ottimo per la SEO e per i link condivisibili) e pulsante "Azzera Tutto".
    *   **Design Top-Level:** Opzioni estensive in Elementor per modificare sfondi, colori, arrotondamenti e le ombreggiature del modulo.

## 📦 Installazione e Setup Breve

1.  Carica la cartella `lytta-elementor-filter` nella directory `/wp-content/plugins/` di WordPress (o carica lo `.zip` tramite la Dashboard).
2.  Attiva il plugin tramite la schermata 'Plugin' in WordPress.
3.  Crea una pagina con Elementor.
4.  Inserisci un widget **Loop Grid (Griglia di loop)** e nella sezione Query -> `ID Query`, scrivi un identificativo (es. `my_custom_grid`).
5.  Inserisci il widget **Lytta Advanced Filter** presente nella barra di Elementor.
6.  Nel pannello del filtro, scrivi lo stesso identificativo (`my_custom_grid`) nel campo **Cerca Query ID (Target)**.
7.  Aggiungi le voci che desideri filtrare e personalizzane lo stile!

## ⚙️ Requisiti

*   WordPress 5.8+
*   Elementor 3.0+
*   Elementor Pro
*   Advanced Custom Fields (opzionale, ma raccomandato)

## 📎 Riferimenti

Sviluppato e manutenuto da **[Lytta.it](https://lytta.it)**. Per assistenza o implementazioni personalizzate, contattaci.
