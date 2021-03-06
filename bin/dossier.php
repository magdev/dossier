<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 magdev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    magdev
 * @copyright 2018 Marco Grätsch
 * @package   magdev/dossier
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require(dirname(__DIR__).'/src/bootstrap.php');
define('DOSSIER_LOGO', 'DQogX19fX19fX18gICAgICAgICAgICAgICAgICAgICAgLl9fICAgICAgICAgICAgICANCiBcX19fX19fIFwgICBfX19fICBfX19fX18gX19fX198X198IF9fX19fX19fX19fIA0KICB8ICAgIHwgIFwgLyAgXyBcLyAgX19fLy8gIF9fXy8gIHwvIF9fIFxfICBfXyBcDQogIHwgICAgYCAgICggIDxfPiApX19fIFwgXF9fXyBcfCAgXCAgX19fL3wgIHwgXC8NCiAvX19fX19fXyAgL1xfX19fL19fX18gID5fX19fICA+X198XF9fXyAgPl9ffCAgIA0KICAgICAgICAgXC8gICAgICAgICAgIFwvICAgICBcLyAgICAgICAgXC8gICAgICAgDQo=');

use Magdev\Dossier\Application;
use Magdev\Dossier\Command;

$app = new Application('dossier.phar', '1.0.0');
$app->add(new Command\Dossier\DossierBuildCommand());
$app->add(new Command\Dossier\DossierInitCommand());
$app->add(new Command\Dossier\DossierStatusCommand());

$app->add(new Command\Create\CreateIntroCommand());
$app->add(new Command\Create\CreatePersonCommand());
$app->add(new Command\Create\CreateCvCommand());
$app->add(new Command\Create\CreateProjectCommand());

$app->add(new Command\Config\ConfigShowCommand());
$app->add(new Command\Config\ConfigGetCommand());
$app->add(new Command\Config\ConfigSetCommand());
$app->add(new Command\Config\ConfigUnsetCommand());

$app->add(new Command\Cache\CacheClearCommand());
$app->add(new Command\Server\ServerStartCommand());
$app->add(new Command\Dev\PharExtractCommand());

$app->run();


