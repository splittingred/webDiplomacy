<?php
/*
    Copyright (C) 2004-2010 Kestas J. Kuliukas

	This file is part of webDiplomacy.

    webDiplomacy is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    webDiplomacy is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with webDiplomacy.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package Base
 */

use Diplomacy\Controllers\DashboardController;
use Diplomacy\Controllers\IntroController;
use Diplomacy\Services\Router;

require_once dirname(__FILE__) . '/header.php';
require_once ROOT_PATH . 'lib/message.php';
require_once ROOT_PATH . 'objects/game.php';
require_once ROOT_PATH . 'gamepanel/gamehome.php';
require_once ROOT_PATH . 'lib/libHome.php';

if (!empty($_REQUEST['q']))
{
    (new Router())->route();
}
else
{
    global $app;
    $User = $app->make('user');
    if (!$User->isAuthenticated())
    {
        $controller = new IntroController();
        echo $controller->render();
    }
    else
    {
        $controller = new DashboardController();
        echo $controller->render();
    }
}