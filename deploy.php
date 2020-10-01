<?php
namespace Deployer;

require 'recipe/laravel.php';

set('bin/php', function () {
    return '/usr/bin/php7.2';
});

// Project name
set('application', 'my_project');

// Project repository
set('repository', 'git@bitbucket.org:luis_vital/eloquent-posto-paggo.git');

//set deploy branch
set('branch', 'master');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', [
    '.env'
]);
add('shared_dirs', [
    'storage'
]);

// Writable dirs by web server
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

set('composer_options', 'install --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction --no-scripts');


// Hosts

host('deploy@145.14.134.109')
    ->port(2222)
    ->set('deploy_path', '/var/www/html/postopago');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

