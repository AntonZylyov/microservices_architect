# Архитектор программного обеспечения
Квинтэссенция всех [предыдущих домашек](homeworks.md) объединенная в одном прекрасном наборе микросервисов.

## Запуск

### Запуск мониторинга

Если неймспейса monitoring нет, создать его:
```
kubectl create namespace monitoring
```

Если нужно, добавить необходимую репку:
```
helm repo add stable https://charts.helm.sh/stable
```

Установить prometheus и grafana:
```
helm install prom stable/prometheus-operator -f k8s/prometheus.yaml -n monitoring --atomic
```

Запустить prometheus:
```
kubectl port-forward service/prom-prometheus-operator-prometheus 9090  -n monitoring
```

Теперь prometheus доступен тут: [http://localhost:9090/](http://localhost:9090/)

Запустить grafana:
```
kubectl port-forward service/prom-grafana 9000:80 -n monitoring
```

Теперь grafana доступна тут: [http://localhost:9000/](http://localhost:9000/)

> логин: admin
> 
> пароль: prom-operator

Можно зайти в http://localhost:9000/dashboard/import и импортировать [файл конфигурации](grafana/dashboard.json) чтобы мониторить состояние кластера.

### Запуск сервисов

Если был штатный ингрес, то загасить его:
```
minikube addons disable ingress
```

Если неймспейса myapp нет, надо его создать:

```
kubectl create namespace myapp
```

Поднимаем nginx-ingress:
```
helm install nginx stable/nginx-ingress -f k8s/nginx-ingress.yaml  -n myapp
```

Теперь можно установить приложение. Назовем его app, в неймспейсе myapp:

```
helm install app ./helm -n myapp
```

Все готово, приложение доступно тут: 

Регистрация: http://bit.homework/api/client/register_client
 
Создание заказа: http://bit.homework/api/order/create_order

Можно импортировать [коллекцию запросов](postman/collection.json) в Postman, запустить и убедиться что все хорошо:

![Postman](final/docs/postman.png)

И заодно в графане посмотреть на графики:

![Postman](final/docs/grafana.png)

## Архитектура

api gateway

взаимодействие сервисов