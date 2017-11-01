# config valid for current version and patch releases of Capistrano
lock "~> 3.10.0"

set :application, "bilbot"
set :repo_url, "git@github.com:kronosnhz/Bilbot.git"

# Default deploy_to directory is /var/www/my_app_name
set :deploy_to, "/home/aitor/bilbot"

set :keep_releases, 3

set :composer_install_flags, '--no-dev --no-interaction --optimize-autoloader'

namespace :deploy do
  after :starting, 'composer:install_executable'
end