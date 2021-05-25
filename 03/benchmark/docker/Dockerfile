FROM alpine
RUN apk add --no-cache apache2-utils
CMD ab -n ${AB_REQUEST_COUNT} -c ${AB_REQUEST_CONCURRENCY} -t ${AB_TIMEOUT} ${AB_URL}

