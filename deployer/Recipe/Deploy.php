<?php
namespace nexxo\Deployer\Recipe;

use function Deployer\get;
use function Deployer\has;
use function Deployer\run;
use function Deployer\set;
use function Deployer\task;
use function Deployer\test;

class Deploy {
    static function init(){
        set('allow_anonymous_stats', false);

        task('deploy:copy_dirs', function () {
            if (has('previous_release')) {
                foreach (get('copy_dirs') as $dir) {
                    if (test("[ -d {{previous_release}}/$dir ]")) {
                        run("mkdir -p {{release_path}}/$dir");
                        run("rsync -q -av {{previous_release}}/$dir/ {{release_path}}/$dir");
                    }
                }
            }
        });
    }
}