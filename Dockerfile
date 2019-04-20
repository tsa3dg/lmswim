FROM alpine
MAINTAINER Taylor Arnold <administrator@lmswim.x10host.com>

RUN apk update && apk add git composer curl php-xml php-simplexml php-pdo php-tokenizer php-curl php-dom
COPY ./lmswim /home/lmswim
WORKDIR /home/lmswim
ENTRYPOINT ["/bin/sh"]
