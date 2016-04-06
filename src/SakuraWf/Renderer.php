<?php

/*
 * Sakura Workflow version 1.0.0
 * Copyright (C) 2016 PocketSoft, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see [http://www.gnu.org/licenses/].
 */

namespace SakuraWf;

class Renderer
{
    private $model;
    private $page;
    private $view;
    private $labels;
    private $errors;

    public function __construct($model, $page, $action)
    {
        $this->model = $model;
        $this->page = $page;
        $this->labels = $this->model->getLabels();
        $this->errors = $this->model->getErrors();

        $view = $this->model->getView();
        if (empty($view)) {
            $this->view = $page . '/' . $action;
        } else {
            $this->view = $view;
        }
        $this->view .= '.phtml';
    }

    public function render()
    {
        $layout = $this->model->getLayout();
        if (empty($layout)) {
            $this->getContent();
        } else {
            $this->partial($layout);
        }
    }

    public function partial($path)
    {
        include_once PROJECT_ROOT . '/views/' . $path;
    }

    public function getContent()
    {
        $this->partial($this->view);
    }

    public function getUrl($url)
    {
        if (strpos($url, '.') === false) {
            $url = APP_ROOT . '/' . $url;
        } else {
            $url = WEB_ROOT . '/' . $url;
        }

        return $url;
    }
}
