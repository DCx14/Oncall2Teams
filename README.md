# Oncall2Teams


## Description:

Contexte et Problématique
Les systèmes de monitoring comme Alertmanager, intégrés à Grafana OnCall, génèrent des alertes précieuses pour la gestion proactive des infrastructures informatiques. Toutefois, ces alertes ne sont pas toujours formatées pour être directement relayées vers la plateformes de collaboration Microsoft Teams. Pour remédier à cela, oncall2teams permet de recevoir les webhooks de Grafana OnCall, de les transformer au bon format, et de les envoyer vers Microsoft Teams, assurant ainsi une communication claire et efficace des alertes.

Réception des Webhooks :

Un serveur web, est configuré pour recevoir des requêtes POST contenant les webhooks envoyés par Grafana OnCall.
Le serveur est capable de traiter les données JSON et de gérer les requêtes.

Le programme extrait les informations pertinentes des webhooks reçus (comme le titre et le message de l'alerte).
Les données sont reformulées pour être présentées dans un format clair et concis, adapté à Microsoft Teams.

## Installation docker


```shell
docker build -t oncall2teams .
docker run -d  -p 8989:80 oncall2teams
```

Ou directement depuis dockerhub

```shell
docker run --name oncall2teams  -p 80:80 -d 42069789/oncall2teams:latest 
```

## Installation pour kubernetes

```shell
git clone https://github.com/DCx14/Oncall2Teams.git
helm install -f values.yaml  oncall2teams ../Helm/ -n monitoring
```
## Fonctionnement

COnfigurer votre Webhook sur Oncall avec pour destination oncall2teams.
Dans la partie "Webhook Headers" coller l'URL du Webhook du canal Teams en format JSON

exemple : 

```shell
{
  "webhook-url": "https://zob.webhook.office.com/webhookb2/767ddadb-b3454-437e-ba9c-aa986c0c92f7@9ced4109-6732-4295-8d3f-n678-371aae263/IncomingWebhook/b1ffd782etf3dh896dfc41ff1c46b7/3F0f5705-7864-443c-8c8c-b2a4855d27cf"
}
```
<img alt="Uptime Badge" src="https://imgur.com/a/rY6T8Uj">

https://imgur.com/a/rY6T8Uj

