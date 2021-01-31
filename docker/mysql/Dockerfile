FROM mysql:8.0

RUN apt update && apt install -y locales \
    && rm -rf /var/lib/apt/lists/* \
    && echo "ja_JP.UTF-8 UTF-8" > /etc/locale.gen \
    && locale-gen ja_JP.UTF-8 \
    && mkdir -p /var/log/mysql/ \
    && touch /var/log/mysql/mysqld.log

ENV LC_ALL ja_JP.UTF-8
