FROM ubuntu:19.10

RUN apt-get update && apt-get install -y \
  advancecomp \
  build-essential \
  curl \
  doxygen \
  git \
  ruby-full \
  zlib1g-dev \
  && true && \
  gem install github-pages bundler \
  && rm -rf /var/lib/apt/lists/* && apt-get clean
