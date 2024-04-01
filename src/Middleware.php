<?php

namespace Vitafeu\EasyMVC;

class Middleware {
    protected function redirect($path) {
        header("Location: $path");
        exit();
    }
}