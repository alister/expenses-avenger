# config valid only for Capistrano 3.1
lock '3.3.5'
set :repo_url, "git@bitbucket.org:alisterb/toptal-expenses.git"

# Default branch is :master
#ask :branch, proc { `git rev-parse --abbrev-ref HEAD`.chomp }.call

# Default deploy_to directory is /var/www/my_app
# - set in the stage ./deploy/*.rb

# Default value for :scm is :git
set :scm, :git
set :deploy_via,  :remote_cache

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :log_level is :debug
# options: :debug :info :warn :error :fatal
set :log_level, :debug

# Default value for keep_releases is 5
set :keep_releases, 20

# Default value for :pty is false
# set :pty, true

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Name used by the Web Server
# (www-data for Apache on Ubuntu, 'apache' on CentOS)
set :file_permissions_users, ["www-data"]
set :webserver_user,        "www-data"

#set :composer_install_flags, '--no-dev --no-interaction --optimize-autoloader --prefer-dist ' # --quiet
set :composer_install_flags, '--no-dev --no-interaction --optimize-autoloader'  #--quiet

# Symfony config file path
set :app_config_path,       fetch(:app_path) + "/config"
set :cache_path,            fetch(:app_path) + "/cache"
set :log_path,              fetch(:app_path) + "/logs"
set :sessions_path,         fetch(:app_path) + "/sessions"

# Default value for :linked_files is []
set :linked_files, %w{app/config/parameters.yml.dist}
# Default value for linked_dirs is []
set :linked_dirs, [fetch(:log_path), fetch(:sessions_path)]

# Method used to set permissions (:chmod, :acl, or :chown)
set :permission_method,  :acl
# Execute set permissions
set :use_set_permissions, true
set :file_permissions_paths,   [fetch(:log_path), fetch(:cache_path), fetch(:sessions_path)]

# Symfony console path
set :symfony_console_path, fetch(:app_path) + "/console"
set :symfony_console_flags, "--no-debug"

# Assets install path
set :assets_install_path,   fetch(:web_path)
# Assets install flags
#set :assets_install_flags,  '--symlink'
set :assets_install_flags,  ''   # copy, not symlink
# Assetic dump flags
set :assetic_dump_flags,  ''

fetch(:default_env).merge!(symfony_env: fetch(:symfony_env))

# https://github.com/mydrive/capistrano-deploytags
set :deploytag_time_format, "%Y%m%d-%H%M%S"

#########

# UGLY HACKS for deployment on the local dev server (Ubuntu)
SSHKit.config.command_map[:composer] = "SYMFONY_ENV=#{fetch(:symfony_env)} /usr/local/bin/composer " #-vvv

namespace :deploy do

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
      # Here we can do anything such as:
      # within release_path do
      #   execute :rake, 'cache:clear'
      # end
    end
  end

  desc "Overwrite the restart task because symfony doesn't need it."
  task :restart do ; end

  desc "Overwrite the stop task because symfony doesn't need it."
  task :stop do ; end

  task :set_rails_env do
    set :symfony_env, (fetch(:symfony_env) || fetch(:stage))
  end

  task :create do
    invoke 'symfony:command', 'doctrine:database:create', '--no-interaction'
  end

  desc "setFacl"
  task :setFacl do
    on roles(:app) do
      #within release_path do
        execute "setfacl -R  -m u:www-data:rwX -m u:root:rwX #{release_path}/app/cache #{release_path}/app/logs"
        execute "setfacl -dR -m u:www-data:rwX -m u:root:rwX #{release_path}/app/cache #{release_path}/app/logs"
      #end
    end
  end
end

before "deploy:updated",    "deploy:set_permissions:acl"
before "deploy:publishing", "deploy:set_permissions:acl"
before "deploy:publishing", "deploy:setFacl"

after  'deploy:updated',    'symfony:assetic:dump'

# remove the composer:install_executable task - we prefer to use a system-installed composer
Rake::Task['deploy:updated'].prerequisites.delete('composer:install_executable')

# Enable Rollbar deployment tracking
# https://rollbar.com/docs/deploys_capistrano/
set :rollbar_token, 'b57a5f37f0c74cfab2f7fafcdda6326a'
set :rollbar_env, Proc.new { fetch :stage }
set :rollbar_role, Proc.new { :app }
