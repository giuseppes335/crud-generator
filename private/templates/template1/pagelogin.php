<?php

require_once 'relation.php';


class Pagelogin extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->header = $params['header'];

        $this->lang = $params['language'];

        $this->title = $params['title'];

        $this->content = $params['content'];

        $this->footer = $params['footer'];

    }


    function reset() {

        if (method_exists($this->content, 'reset')) {

            $this->content->reset();

        }

    }

    function bootstrap() {

        if (method_exists($this->content, 'bootstrap')) {

            $this->content->bootstrap();

        }

    }


    function get() {
        
        ob_start();

        $lang = $this->lang;

        $title = $this->title;

        $application_host = $this->application->host;

        $application_path = $this->application->path;

        echo <<<EOT
        <!doctype html>
        <html lang="$lang">
EOT;

        $this->header->get();

        echo <<<EOT
        <body style="display: flex; flex-direction: column">
        <header class="header" style="height: 50px; display: flex; align-items: center; justify-content: space-between;">
        <a class="logo" href="">$title</a>
        </header>
        <main style="flex-grow: 1;">
        <div style="position: relative; height: 100%;">
        <div id="content" class="content">
EOT;

        $this->content->get();

        echo <<<EOT
        </div>
        </div>
        </main>
EOT;

        $this->footer->get();

        echo <<<EOT
        </body>
        </html>
EOT;
        
        $output = ob_get_contents();

        ob_end_clean();

        echo $output;

    }

    function post() {

    }

    function put() {

    }

    function delete() {
        
    }

}