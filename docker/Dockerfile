FROM phusion/baseimage:0.9.13

ENV HOME /root

EXPOSE 80
EXPOSE 22

CMD /sbin/my_init

RUN apt-get update
RUN apt-get install -y \
        git curl \
        php5-cli php5-fpm php5-intl php5-mcrypt php5-json php5-mysql php5-sqlite php5-curl \
        nginx \
        mysql-server \
        redis-server

ADD config/nginx.conf   /etc/nginx/nginx.conf
ADD config/php-fpm.conf /etc/php5/fpm/pool.d/www.conf
ADD service/nginx.sh    /etc/service/nginx/run
ADD service/mysql.sh    /etc/service/mysql/run
ADD service/redis.sh    /etc/service/redis/run
ADD service/php-fpm.sh  /etc/service/php-fpm/run
ADD service/jobs.sh     /etc/service/jobs/run
ADD start.sh            /etc/my_init.d/gitonomy-start.sh

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
