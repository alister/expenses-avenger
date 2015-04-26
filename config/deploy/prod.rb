# bundle exec cap prod deploy --dry-run

set :symfony_env,  "prod"
set :SYMFONY_ENV,  "prod"
set :application, "expenses"

role :app, %w{ root@expenses.abulman.co.uk }
##role :web, %w{ deploy@expenses.abulman.co.uk }
##role :db,  %w{ deploy@expenses.abulman.co.uk }

set :deploy_to,     "/var/www/vhosts/expenses"
set :domain,        "expenses.abulman.co.uk"

# remove app_*.php files we don't want to be able to use
set :controllers_to_clear, [ "app_dev.php", "app_test.php" ]
