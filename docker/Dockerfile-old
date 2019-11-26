ARG phpImg=php
FROM $phpImg
ARG port=19290
ARG uid=90
ARG gid=90
RUN groupadd -g "$gid" grima && \
    useradd -u "$uid" -g "$gid" --home /home/grima -m grima
USER $uid
COPY . /home/grima
WORKDIR /home/grima
EXPOSE $port
VOLUME /home/grima/persist
ENV DATABASE_URL=sqlite:/home/grima/persist/grima.sql PORT=$port
CMD exec php -S 0.0.0.0:$PORT
