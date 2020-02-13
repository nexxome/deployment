<?php
namespace nexxo\Deployer\Recipe;

use Deployer\Exception\GracefulShutdownException;
use function Deployer\input;
use function Deployer\task;
use function Deployer\run;
use function Deployer\test;

class Symfony {
    static function init(){
        // Tasks
        task('schema:update', function () {
            run('{{env_vars}} {{bin/php}} {{bin/console}} doctrine:schema:update --force {{console_options}}');
        });
        task('cache:twig:clear', function () {
            run("rm -rf {{release_path}}/var/cache/prod/twig");
        });

        // translation tasks
        task('deploy:translation:check_lock', function () {
            $locked = test("[ -f {{deploy_path}}/current/translation.lock ]");

            if ($locked) {
                $stage = input()->hasArgument('stage') ? ' ' . input()->getArgument('stage') : '';

                throw new GracefulShutdownException(
                    "WARNING: Translation files have changed.\n" .
                    sprintf('Check for changes and then execute "dep deploy:translation:unlock%s" to unlock.', $stage)
                );
            }
        });
        task('deploy:translation:unlock', function () {
            run("rm -f {{deploy_path}}/current/translation.lock");
        });
    }
}