<?php
namespace nexxo\Deployer\Recipe;

use function Deployer\task;
use function Deployer\run;

class Symfony {
    static function init(){
        // Tasks
        task('schema:update', function () {
            run('{{env_vars}} {{bin/php}} {{bin/console}} doctrine:schema:update --force {{console_options}}');
        });
        task('cache:twig:clear', function () {
            run("rm -rf {{release_path}}/var/cache/prod/twig");
        });
    }
}