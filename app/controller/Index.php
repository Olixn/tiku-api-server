<?php

namespace app\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return redirect('https://www.baidu.com');
    }

}
