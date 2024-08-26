<?php

namespace App\Core;

class View
{
    /**
     * Render view
     * @param array $params - associative array. keys - [content_view, layout_view, title, data]
     * @return void
     */
    public function render($params)
    {
        $content_view = $params['content_view'];
        $layout_view = $params['layout_view'] ?? 'layout_view.php';
        $title = $params['title'] ?? 'MyChat';
        $data = $params['data'] ?? null;

        include APP_DIR . "/views/layout/$layout_view";
    }
}
