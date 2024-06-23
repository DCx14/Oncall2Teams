# Oncall2Teams


## Description:

Contexte et Problématique
Les systèmes de monitoring comme Prometheus, intégrés à Grafana OnCall, génèrent des alertes précieuses pour la gestion proactive des infrastructures informatiques. Toutefois, ces alertes ne sont pas toujours formatées pour être directement relayées vers la plateformes de collaboration Microsoft Teams. Pour remédier à cela, oncall2teams permet de recevoir les webhooks de Grafana OnCall, de les transformer au bon format, et de les envoyer vers Microsoft Teams, assurant ainsi une communication claire et efficace des alertes.

Fonctionnalités Principales:

Réception des Webhooks :

Un serveur web, est configuré pour recevoir des requêtes POST contenant les webhooks envoyés par Grafana OnCall.
Le serveur est capable de traiter les données JSON et de gérer les requêtes.

Le programme extrait les informations pertinentes des webhooks reçus (comme le titre et le message de l'alerte).
Les données sont reformulées pour être présentées dans un format clair et concis, adapté à Microsoft Teams.

## Installation docker

