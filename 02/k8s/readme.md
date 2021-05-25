Перед запуском манифестов из этой папки нудно установить mysql из helm:

- добавить репозиторий:

`helm repo add stable https://kubernetes-charts.storage.googleapis.com/`

`helm repo update`

- установить чарт mysql: 

`helm install mysqldb --set mysqlUser=myuser,mysqlPassword=mypasswd,mysqlDatabase=myapp_v2 stable/mysql`