# Tsugi LMS Docker Image
# Based on Ubuntu 24.04 with PHP 8.4, Apache, and MariaDB
#
# Build: docker build -t tsugi .
# Run: docker run -p 8080:80 -p 3306:3306 -e TSUGI_SERVICENAME=MyTsugi tsugi

FROM ubuntu:24.04

LABEL maintainer="Tsugi Docker"
LABEL description="Tsugi LMS - PHP/MySQL Learning Tools Interoperability Platform"

# Prevent interactive prompts during package installation
ENV DEBIAN_FRONTEND=noninteractive
ENV HOME=/root
ENV TZ=UTC

# Exposed ports: HTTP, HTTPS, MySQL
EXPOSE 80 443 3306

WORKDIR /root

# =============================================================================
# SYSTEM PACKAGES AND DEPENDENCIES
# =============================================================================

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
    # Essential build tools
    build-essential \
    software-properties-common \
    apt-utils \
    ca-certificates \
    # Development utilities (including git as requested)
    git \
    curl \
    wget \
    zip \
    unzip \
    vim \
    htop \
    man \
    byobu \
    # Database client
    mysql-client \
    # NFS support (for shared storage)
    nfs-common \
    # SQLite for local development
    sqlite3 \
    # Cron for scheduled tasks
    cron \
    # Apache web server
    apache2 \
    && rm -rf /var/lib/apt/lists/*

# =============================================================================
# PHP 8.4 INSTALLATION
# =============================================================================

# Add PHP repository for Ubuntu 24.04
RUN add-apt-repository -y ppa:ondrej/php && \
    apt-get update && \
    apt-get install -y --no-install-recommends \
    php8.4 \
    libapache2-mod-php8.4 \
    php8.4-mysql \
    php8.4-curl \
    php8.4-mbstring \
    php8.4-zip \
    php8.4-xml \
    php8.4-gd \
    php8.4-apcu \
    php8.4-intl \
    php8.4-memcached \
    php8.4-memcache \
    php8.4-sqlite3 \
    php8.4-common \
    php8.4-opcache \
    php8.4-imagick \
    php8.4-cli \
    php8.4-soap \
    php8.4-imap \
    && rm -rf /var/lib/apt/lists/*

# Enable additional PHP modules
RUN phpenmod mysqlnd pdo_mysql intl sqlite3 pdo_sqlite

# =============================================================================
# MARIADB INSTALLATION
# =============================================================================

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    mariadb-server \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# =============================================================================
# NODE.JS AND COMPOSER INSTALLATION
# =============================================================================

# Install Node.js LTS
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# =============================================================================
# APACHE CONFIGURATION
# =============================================================================

# Enable required Apache modules
RUN a2enmod rewrite dir expires headers ssl

# Configure Apache for Tsugi
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# =============================================================================
# TSUGI INSTALLATION
# =============================================================================

# Clone Tsugi repository
RUN git clone https://github.com/tsugiproject/tsugi.git /var/www/html/tsugi

# Set ownership
RUN chown -R www-data:www-data /var/www/html

# Create data directories for blob storage
RUN mkdir -p /tsugi-data/blobs && \
    chown -R www-data:www-data /tsugi-data

# =============================================================================
# CONFIGURATION FILES
# =============================================================================

# Copy configuration and startup scripts
COPY docker-entrypoint.sh /usr/local/bin/
COPY config-template.php /root/config-template.php
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# =============================================================================
# ENVIRONMENT VARIABLES
# =============================================================================

# Database configuration (can be overridden at runtime)
ENV TSUGI_DB_HOST=127.0.0.1
ENV TSUGI_DB_PORT=3306
ENV TSUGI_DB_NAME=tsugi
ENV TSUGI_DB_USER=ltiuser
ENV TSUGI_DB_PASSWORD=ltipassword

# Application configuration
ENV TSUGI_SERVICENAME=TSUGI
ENV TSUGI_WWWROOT=http://localhost:8080/tsugi
ENV TSUGI_ADMIN_PASSWORD=admin
ENV TSUGI_DEVELOPER_MODE=true
ENV TSUGI_TIMEZONE=UTC

# Security secrets (should be overridden in production!)
ENV TSUGI_COOKIE_SECRET=change-me-cookie-secret-1234567890
ENV TSUGI_SESSION_SALT=change-me-session-salt-1234567890
ENV TSUGI_MAIL_SECRET=change-me-mail-secret-1234567890

# =============================================================================
# ENTRYPOINT
# =============================================================================

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache"]
