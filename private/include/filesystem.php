<?php

class Filesystem {

    function __construct() {

    }

    function del_tree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->del_tree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

}