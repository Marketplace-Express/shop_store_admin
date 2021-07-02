#
# Shop store admin service Dockerfile
# Copyrights to Wajdi Jurry <github.com/wajdijurry>
#
FROM php:7.3-fpm

LABEL maintainer="Wajdi Jurry <github.com/wajdijurry>"

# Important! To prevent this warning "debconf: unable to initialize frontend"
ARG DEBIAN_FRONTEND=noninteractive

# Update apt repos
RUN echo "Updating repos ..." && apt-get -y update > /dev/null
RUN apt-get install -yq apt-utils 2>&1 | grep -v "debconf: delaying package configuration, since apt-utils is not installed"

# Return working directory to its default state
WORKDIR /src

# Copy project files to container
RUN echo "Copying project files ..."
ADD . ./

# Install required tools and dependencies
RUN chmod +x utilities/install-dependencies.sh && ./utilities/install-dependencies.sh

# Install required PHP extensions
RUN chmod +x utilities/install-php-extensions.sh && ./utilities/install-php-extensions.sh

# Copy PHP extensions to docker image
RUN cp -a php_extensions/. /usr/local/etc/php/conf.d/

# Install composer & dependencies
RUN echo "Installing Composer" && rm -rf vendor composer.lock && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer clearcache && \
    composer install

ENTRYPOINT ["/bin/bash", "utilities/docker-entrypoint.sh"]
