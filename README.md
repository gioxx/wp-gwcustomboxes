> [!NOTE]
>
> https://gioxx.github.io/wp-gwcustomboxes/plg-customboxes.json

## Changelog

| Data     | Note                                                         |
| -------- | ------------------------------------------------------------ |
| 14/12/23 | - Fix: adeguo gli articoli \"*Sponsored*\" e mi baso sui tag abbandonando l'idea della categoria. |
| 4/12/23  | - Fix: modifico il rilevamento della categoria \"*Sponsored*\" e la parte di codice CSS per l'icona del plugin.<br />- Improve: modifico il comportamento del box \"*Sponsored*\" per specificare che tipo di sponsorizzazione si applica all'articolo (come già faccio per il Banco Prova). |
| 23/11/23 | - Change: semplici fix estetici (testo).                     |
| 17/5/23  | - Bugfix: metto a posto i problemi generati dal rilevamento dei campi personalizzati degli articoli.<br />- Bugfix: metto a posto JSON riepilogativo.<br />- Bugfix: correggo problema blocco proposto in ogni \"content\" (abbandono l'idea  della funzione dedicata, ne comincio a fare una unica per tutti i  blocchi (htmlContent)).<br />- Change: aggiornamenti automatici integrati con il rilascio via Github.<br />- Change: rimuovo completamente i riferimenti ai progetti sponsorizzati (pulizia codice, non più utile).<br />- Change: elimino il file di changelog vecchio (CHANGELOG.MD) e migro tutto il contenuto in questo JSON informativo.<br />- Improvement: aggiungo icona personalizzata per il plugin.<br />- Improvement: completo migrazione a funzione \"boxSelection\".<br />- Improvement: pulizia e ottimizzazione codice.<br />- Improvement: immagine di copertina di [Erda Estremera](file:///"https://unsplash.com/@erdaest?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText\") su [Unsplash](file:///"https://unsplash.com/it/foto/sxNt9g77PE0?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText\"). |
| 8/12/20  | - Change: ho spostato l'icona del blocco articolo non aggiornato di recente. |
| 22/11/20 | - Change: nuovo blocco per aggiornamento dell'articolo.<br />- Improvement: rifacimento completo della grafica dei custom boxes. |
| 7/11/20  | - Change: modifico il box del banco prova per essere un po' meno invadente rispetto al passato, più compatto. Me gusta, lo applico a tutti tranne al blocco di attenzione per contenuto \"vecchio\", porto quindi la  versione del plugin CustomBoxes alla 0.6. |
| 8/5/20   | - Improvement: carico qui dentro JavaScript per la chiusura box custom. |
| 19/4/20  | - Change: modifico icona pillole.                            |
| 17/4/20  | - Change: il riconoscimento dell'articolo \"*Sponsored*\" passa sotto categoria dedicata, modifico quindi has_tag con has_category. Modifico anche class=\"*titolo_custombox*\" per i titoli dei box alert. |
| 9/4/20   | - Bugfix: corretti i custom boxes per integrare i tag p mancanti. |
| 23/9/19  | - Change: leggera modifica del blocco Sponsored nel caso di richiesta esplicita di modifica articolo. |
| 25/6/19  | - Improvement: tutti gli URL immagini diretti sono stati sostituiti con quelli gestiti da WordPress Media. |
| 19/6/19  | - Bugfix: modifico blocco Android's Corner e metto a posto i tag ALT non corretti. |
| 20/3/19  | - Improvement: aggiungo \"A piccoli passi\" (blocco articoli con tag). |
| 11/10/18 | - Bugfix: ho azzerato le modifiche tra is_archive, is_home e is_single per i box custom, tutti in fondo all'articolo (per evitare problemi nelle anteprime nei box di Sidebar), rimesso ano anche all'indentazione codice e spaziature. |
| 4/10/18  | - Change: ho spostato la posizione dei Custom Boxes Pillole. |
| 17/9/18  | - Change: ho spostato la posizione dei Custom Boxes Android / Sponsorizzato / Consigli per gli acquisti. |
| 16/9/18  | - Change: modifica del blocco Sponsored (autore sottoscritto o ospite di  vecchia data), includo icona FontAwesome per indicare il contenuto sponsorizzato. |
| 19/6/18  | - Change: pura modifica estetica del box Pillole.            |
| 17/6/18  | - Improvement: aggiungo il Blocco Prodotto anche per il Banco Prova Console. |
| 8/6/18   | - Improvement: migrato a nuovo AlertBox le Pillole.          |
| 2/6/18   | - Improvement: migrato a nuovo AlertBox OldPost. Pulizia codice non più usato. |
| 22/5/18  | - Improvement: migrato a nuovo AlertBox MRL + Disclaimer #BancoProva. |
| 20/5/18  | - Improvement: modificato box sponsored post per presentare a video il nuovo AlertBox. |
| 15/1/18  | - Improvement: class \"*bancoprova*\" nei div dei test su strada, per intercettarlo e pulirlo dalla Telegram Instant View. Modificato puntamento immagine e sostituito con thumb Fighorse150x150.pg. |
| 11/1/18  | - Improvement: modificato \"*i miei pensieri*\" con \"*le mie opinioni*\" nel blocco disclaimer del banco prova. |
| 23/10/17 | - Improvement: ho modificato un dettaglio sul caricamento immagine di Mario per il box pillole. |
| 20/10/17 | - Change: ho provato a giocare con il blocco donazione PayPal.me, mi fa schifo, ciao e grazie per il pesce. |
| 3/3/17   | - Improvement: puntamenti a HTTPS.                           |
| 20/12/16 | - Improvement: ho modificato le categorie legate al progetto sponsorizzato e ai singoli post sponsorizzati, rifatto anche il div dello Sponsored post, utilizzo il nuovo wrapper. |
| 11/7/16  | - Change: ho modificato una sola parola nel box materiale sponsorizzato. |
| 13/6/16  | - Improvement: includo nuovo box per materiale sponsorizzato (se rimane a me, viene restituito o comprato di tasca mia). |
| 4/4/16   | - Change: il logo dello sponsor (progetto sponsorizzato) può avere al massimo un width di 250px. |
| 18/3/16  | - Improvement: modificato il blocco della vecchia pubblicazione. L'ho spostato in coda perché modifica anche il contenuto della sidebar e viene fuori una schifezza agli occhi dei lettori. |
| 15/2/16  | - Change: inserito blocco Press Start Milano.                |
| 30/11/15 | - Bugfix: corretto errore URL recensione VF Smart Ultra 6.   |
| 1/11/15  | - Bugfix: corretti errori su blocco VF Smart Ultra 6.        |
| 28/10/15 | - Change: completate informazioni VF Smart Ultra 6.          |
| 6/10/15  | - Improvement: messo a posto blocco Xperia Z2, creato blocco per VF Smart Ultra 6. |
| 2/9/15   | - Improvement: inseriti i controlli di presenza variabili per i Progetti Sponsorizzati, rimosso blocco di TimeRepublik. |

