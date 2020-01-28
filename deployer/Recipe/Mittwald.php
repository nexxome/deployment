<?php
namespace nexxo\Deployer\Recipe;

use function Deployer\add;
use function Deployer\after;
use function Deployer\run;
use function Deployer\set;
use function Deployer\task;

class Mittwald {
    static function init(){
        // Configuration
        set('bin/php', 'php');
        set('bin/composer', 'composer');
        set('allow_anonymous_stats', false);
        set('git_cache', true);
        add('writable_dirs', []);

        // Tasks
        task('deploy:writable', function(){});
        task('opcache:clear', function(){
            run("touch /etc/php/php.ini");
        });

        after('cleanup', 'opcache:clear');
    }
}