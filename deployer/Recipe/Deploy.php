<?php
namespace nexxo\Deployer\Recipe;

use function Deployer\get;
use function Deployer\has;
use function Deployer\input;
use function Deployer\run;
use function Deployer\set;
use function Deployer\task;
use function Deployer\test;

class Deploy {
    static function init(){
        set('allow_anonymous_stats', false);

        task('deploy:update_code', function(){
            self::updateCode();
        });

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

    public static function updateCode(){
        $remoteCachePath = "{{deploy_path}}/shared/git-remote-cache";

        $repository = trim(get('repository'));
        $branch = get('branch');
        $git = get('bin/git');
        $gitCache = get('git_cache');
        $depth = $gitCache ? '' : '--depth 1';
        $options = [
            'tty' => get('git_tty', false),
            'timeout' => 60*10
        ];

        // If option `branch` is set.
        if (input()->hasOption('branch')) {
            $inputBranch = input()->getOption('branch');
            if (!empty($inputBranch)) {
                $branch = $inputBranch;
            }
        }

        // Branch may come from option or from configuration.
        $at = '';
        if (!empty($branch)) {
            $at = "-b $branch";
        }

        // If option `tag` is set
        if (input()->hasOption('tag')) {
            $tag = input()->getOption('tag');
            if (!empty($tag)) {
                $at = "-b $tag";
            }
        }

        // If option `tag` is not set and option `revision` is set
        if (empty($tag) && input()->hasOption('revision')) {
            $revision = input()->getOption('revision');
            if (!empty($revision)) {
                $depth = '';
            }
        }

        if (!test("[ -d $remoteCachePath ]")) {
            run("$git clone $at $depth -q $repository $remoteCachePath 2>&1", $options);
        }else{
            run("cd $remoteCachePath && $git pull -q 2>&1", $options);
            run("cd $remoteCachePath && $git checkout $branch -q 2>&1", $options);
        }

        run("cd $remoteCachePath && $git archive $branch | tar -x -C {{release_path}} 2>&1", $options);
    }
}