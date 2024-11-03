# Base image to use Composer
FROM composer:2.7.6 AS composer_base

# Base image to use PHP 8.3.7 FPM
FROM php:8.3.7-fpm

# Copy Composer from the base image
COPY --from=composer_base /usr/bin/composer /usr/bin/composer

# Install necessary packages and PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libxml2-dev \
    libonig-dev \
    libpq-dev \
    libzip-dev \
    bash \
    vim \
    bash-completion \
    zsh \
    && docker-php-ext-install intl pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Install additional tools and PHP extensions
RUN apt-get update && apt-get install -y \
    autoconf \
    g++ \
    make \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip sockets \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get purge -y --auto-remove \
    autoconf \
    g++ \
    make \
    && rm -rf /var/lib/apt/lists/*

# Install and configure Xdebug
RUN apt-get update && apt-get install -y \
    $PHPIZE_DEPS \
    && pecl install xdebug-3.3.2 \
    && docker-php-ext-enable xdebug \
    && apt-get purge -y --auto-remove \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_current.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && rm -rf /var/lib/apt/lists/*

# Clean up APT when done.
RUN apt clean && rm -rf /var/lib/apt/lists/*

# Copy Xdebug configuration
COPY ./xdebug-config/xdebug.ini "${PHP_INI_DIR}/conf.d/xdebug.ini"

# Install Oh My Zsh for root user
RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" --unattended

# Set the gnzh theme for root
RUN sed -i 's/ZSH_THEME="robbyrussell"/ZSH_THEME="gnzh"/' /root/.zshrc

# Install Zsh plugins and adjust .zshrc for root
RUN git clone https://github.com/zsh-users/zsh-syntax-highlighting.git /root/.oh-my-zsh/custom/plugins/zsh-syntax-highlighting \
    && git clone https://github.com/zsh-users/zsh-autosuggestions /root/.oh-my-zsh/custom/plugins/zsh-autosuggestions \
    && sed -i 's/plugins=(git)/plugins=(git zsh-syntax-highlighting zsh-autosuggestions)/' /root/.zshrc

# Create the .zsh_history file with proper permissions for root
RUN touch /root/.zsh_history \
    && chmod 644 /root/.zsh_history

# Argument for the UID and GID of the host user
ARG UID=1000
ARG GID=1000

# Create a user with the same UID and GID as the host user
RUN groupadd -g $GID usergroup \
    && useradd -u $UID -g usergroup -m username

# Set the working directory
WORKDIR /var/www/html

# Change ownership of the working directory
RUN chown -R username:usergroup /var/www/html

# Switch to the new user
USER username

# Expose port 9000
EXPOSE 9000

# Command to start PHP-FPM
CMD ["php-fpm"]
