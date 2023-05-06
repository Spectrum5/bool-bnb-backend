# Progetto Finale Boolean | BoolBnb

La sezione BackEnd del progetto è stata sviluppata in Laravel 10.9.0.

## Classe #84 | Team 2
- [Salvatore Capano](https://github.com/SalvatoreCapano)
- [Giada Ortesta](https://github.com/GiadaMarzapane)
- [Sergio Tosku](https://github.com/Spectrum5)
- [Lucia Calenda](https://github.com/CalendaLucia)

## Features
Il progetto è stato sviluppato secondo l'architettura MVC.
L'applicazione mette a disposizione delle rotte, opportunamente protette, a cui è possibile fare chiamate API per richiedere/inviare dati.
I vari controller restituiscono delle risposte composte da una flag status, un messaggio che varia a seconda dell'esito dell'operazione, ed eventualmente dei dati ottenuti dal DB tramite le apposite query.
L'autenticazione è gestita tramite Sanctum.
Tutti i dati inviati sono opportunamente validati tramite le Form Requests apposite.
Le rotte sono protette dal middleware Sanctum.

## Link alla repository FrontEnd
[BoolBnb | FrontEnd](https://github.com/Spectrum5/bool-bnb-frontend)
