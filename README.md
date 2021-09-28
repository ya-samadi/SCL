# SCL
Platform

# Initiation
composer install

# Execution du projet
symfony server:start

# initaition de BS

php bin/console doctrine:database:create

php bin/console make:migration
php bin/console doctrine:migrations:migrate

php bin/console doctrine:fixtures:load

# Les commande bin/console

php bin/console make:controller NameController

