<?php

namespace WS\Site\Library\Composer;

use WS\Core\Library\Composer\CommonTasks;

class Tasks extends CommonTasks
{
    public static function getAssetsSource()
    {
        return realpath(__DIR__ . '/../../Resources/assets');
    }

    public static function getAssetsTarget()
    {
        return 'assets/ws/site';
    }
}
