<?php
/**
 * Controller.php
 * 文件描述
 * Created on 2023/11/3 14:40
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
