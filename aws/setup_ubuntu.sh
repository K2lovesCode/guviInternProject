#!/bin/bash

# Exit on error
set -e

echo "Starting server setup..."

# 1. Update System
echo "Updating system packages..."
sudo apt update -y && sudo apt upgrade -y

# 2. Install Apache
echo "Installing Apache Web Server..."
sudo apt install apache2 -y
sudo systemctl enable apache2
sudo systemctl start apache2

# 3. Install PHP and Extensions
echo "Installing PHP and common extensions..."
# This installs the default PHP version for the Ubuntu release (e.g., 8.1 for 22.04 LTS)
sudo apt install php libapache2-mod-php php-mysql php-xml php-curl php-mbstring php-zip unzip git -y

# Install MongoDB and Redis extensions via apt (easier than pecl)
echo "Installing MongoDB and Redis extensions..."
sudo apt install php-mongodb php-redis -y

# 4. Install Composer
echo "Installing Composer..."
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

# 5. Configure Apache Document Root (Optional - changes default from /var/www/html)
# We will just warn the user to move files to /var/www/html later.

# 6. Restart Apache to load new PHP configurations
sudo systemctl restart apache2

echo "----------------------------------------------------------------"
echo "Setup Complete!"
echo "PHP Version:"
php -v
echo "Composer Version:"
composer --version
echo "----------------------------------------------------------------"
echo "Next Steps:"
echo "1. Clone your repository into /var/www/html (or copy files)."
echo "2. Run 'composer install' in the project directory."
echo "3. Create a .env file with your AWS RDS credentials."
echo "----------------------------------------------------------------"
