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
 
namespace Magdev\Dossier\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;


/**
 * Configuration loader
 * 
 * @author magdev
 */
class ConfigLoader extends FileLoader
{
    private $config = array();
    private $global = array();
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Config\Loader\LoaderInterface::load()
     */
    public function load($resource, $type = null)
    {
        $content = file_get_contents($resource);
        if ($content) {
            $config = Yaml::parse($content);
            
            if (is_array($config)) {
                $this->config = array_replace_recursive($this->config, $config);
                
                if (strstr($resource, PROJECT_ROOT, true) === false) {
                    $this->global = array_replace_recursive($this->global, $config);
                }
            }
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Config\Loader\LoaderInterface::supports()
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yaml' === pathinfo($resource, PATHINFO_EXTENSION);
    }
    
    
    /**
     * Get the config as PHP array sourcecode
     * 
     * @return string
     */
    public function __toString(): string
    {
        return 'array(
            "config" => '.var_export($this->config, true).',
            "global" => '.var_export($this->global, true).'
        )';
    }
}

