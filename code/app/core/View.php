<?php

namespace App\Core;

class View
{
    public function render($content_view, $layout_view = 'layout_view.php', $title = 'MyChat', $data = null)
    {
        include APP_DIR . "/views/layout/$layout_view";
    }
}
