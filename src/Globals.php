<?php

namespace Vitafeu\EasyMVC;

class Globals {
    private static $projectRoot = '';

    public static function setProjectRoot($projectRoot) {
        self::$projectRoot = $projectRoot;
    }

    public static function getProjectRoot() {
        return self::$projectRoot;
    }
}