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
 
namespace Magdev\Dossier\Service;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Finder\Finder;
use Magdev\Dossier\Config\ConfigLoader;
use Magdev\Dossier\Config\Config;
use Magdev\Dossier\Style\DossierStyle;
use Adbar\Dot;

/**
 * Cnfiguration service
 * 
 * @author magdev
 */
class ConfigService
{
    const FORMAT_FLAT_ARRAY = 1;
    const FORMAT_TABLE_ROWS = 2;
    const FORMAT_PHP_ARRAY  = 3;
    
    const VALUE_SCALAR        = 1;
    const VALUE_INDEXED_ARRAY = 2;
    
    /**
     * The config object
     * @var \Adbar\Dot
     */
    protected $config = null;
    
    /**
     * The global config object
     * @var \Adbar\Dot
     */
    protected $global = null;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        if (file_exists(PROJECT_ROOT.'/.env')) {
            $dotenv = new Dotenv();
            $dotenv->load(PROJECT_ROOT.'/.env');
        }
        $this->loadConfig();
        
        mb_internal_encoding(strtoupper($this->config->get('charset')));
    }
    
    
    /**
     * Get the differences between current and global config
     * 
     * @return array
     */
    public function getDiff(array $current = null, array $global = null): array
    {
        if (is_null($current)) {
            $current = $this->config->all();
        }
        if (is_null($global)) {
            $global = $this->global->all();
        }
        
        $diff = array();
        foreach ($current as $key => $value) {
            if (is_array($value)) {
                if (!isset($global[$key]) || !is_array($global[$key])) {
                    $diff[$key] = $value;
                } else {
                    $newDiff = $this->getDiff($value, $global[$key]);
                    if (!empty($newDiff)) {
                        $diff[$key] = $newDiff;
                    }
                }
            } else if (!array_key_exists($key, $global) || $global[$key] !== $value) {
                $diff[$key] = $value;
            }
        }
        return $diff;
    }
    
    
    /**
     * Get the Dot object
     * 
     * @return \Adbar\Dot
     */
    public function getConfig(): Dot
    {
        return $this->config;
    }
    
    
    /**
     * Get the global Dot object
     *
     * @return \Adbar\Dot
     */
    public function getGlobalConfig(): Dot
    {
        return $this->global;
    }
    
    
    /**
     * Delegate method calls to Dot
     * 
     * @param string $method
     * @param array $args
     * @throws \BadFunctionCallException
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (!method_exists($this->config, $method)) {
            throw new \BadFunctionCallException('Unknown method: '.$method);
        }
        return call_user_func_array(array($this->config, $method), $args);
    }
    
    
    /**
     * Get all configuration values
     * 
     * @param int $format One of FORMAT_* constants
     * @return array
     */
    public function all(int $format = self::FORMAT_PHP_ARRAY): array
    {
        switch ($format) {
            case self::FORMAT_FLAT_ARRAY: 
                $data = $this->allFlat(); 
                break;
                
            default:
            case self::FORMAT_PHP_ARRAY: 
                $data = $this->config->all(); 
                break;
        }
        ksort($data, SORT_NATURAL);
        return $data;
    }
    
    
    /**
     * Set a configuration value
     * 
     * @param array|int|string $keys
     * @param mixed $value
     * @param bool $global
     * @return \Magdev\Dossier\Service\ConfigService
     */
    public function set($keys, $value, bool $global = false): ConfigService
    {
        $this->config->set($keys, $value);
        if ($global) {
            $this->global->set($keys, $value);
        }
        return $this;
    }
    
    
    /**
     * Unset a configuration value
     * 
     * @param unknown $keys
     * @return \Magdev\Dossier\Service\ConfigService
     */
    public function unset($keys): ConfigService
    {
        if ($this->config->has($keys)) {
            $this->config->delete($keys);
        }
        if ($this->global->has($keys)) {
            $this->global->delete($keys);
        }
        return $this;
    }
    
    
    /**
     * Save the global configuration
     * 
     * @return \Magdev\Dossier\Service\ConfigService
     */
    public function saveGlobalConfig(): ConfigService
    {
        $yaml = '';
        $config = $this->global->all();
        if (!empty($config)) {
            $yaml = Yaml::dump($config, 4, 3);
        }
        return $this->saveYaml(getenv('HOME').'/.dossier', $yaml);
    }
    
    
    /**
     * Save the local configuration
     * 
     * @return \Magdev\Dossier\Service\ConfigService
     */
    public function saveProjectConfig(): ConfigService
    {
        $yaml = '';
        $config = $this->getDiff();
        if (!empty($config)) {
            $yaml = Yaml::dump($config, 4, 3);
        }
        return $this->saveYaml(PROJECT_ROOT.'/.conf', $yaml);
    }
    
    
    /**
     * Check if a value is stored in global configuration
     * 
     * @param string $path
     * @param mixed $value
     * @return bool
     */
    public function isGlobalConfig(string $path, $value): bool
    {
        if ($this->global->has($path) && $this->global->get($path) == $value) {
            return true;
        }
        return false;
    }
    
    
    /**
     * Save configuration
     * 
     * @return \Magdev\Dossier\Service\ConfigService
     */
    public function save(): ConfigService
    {
        return $this->saveGlobalConfig()
            ->saveProjectConfig()
            ->loadConfig();
    }
    
    
    /**
     * Get all config values as flat array
     * 
     * @return array
     */
    public function allFlat(): array
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->all()), \RecursiveIteratorIterator::SELF_FIRST);
        $path = array();
        $flatArray = array();
        
        foreach ($iterator as $key => $value) {
            $path[$iterator->getDepth()] = $key;
                
            if (!is_array($value)) {
                $flatArray[implode('.', array_slice($path, 0, $iterator->getDepth() + 1))] = $value;
            }
        }
        return $flatArray;
    }
    
    
    /**
     * Get all config values as table rows
     *
     * @param \Magdev\Dossier\Style\DossierStyle $io
     * @param int $booleanWidth
     * @return array
     */
    public function allTable(DossierStyle $io, int $booleanWidth = null): array
    {
        $all = $this->allFlat();
        $rows = array();
        
        foreach ($all as $path => $value) {
            $global = $this->isGlobalConfig($path, $value);
            
            switch (gettype($value)) {
                case 'string':
                    if ($value !== trim($value)) {
                        $value = "'$value'";
                    }
                    break;
                    
                case 'bool':
                case 'boolean':
                    $value = $value ? 'true' : 'false';
                    break;
            }
            
            $rows[] = array($path, $value, $io->align($io->bool($global), $booleanWidth, DossierStyle::ALIGN_CENTER));
        }
        return $rows;
    }
    
    
    /**
     * Find all themes in available directories
     * 
     * @return array
     */
    public function findThemes(bool $all = true): array
    {
        $folders = array(
            PROJECT_ROOT.'/.conf/tpl',
            getenv('HOME').'/.dossier/tpl',
            DOSSIER_ROOT.'/app/tpl'
        );
        
        $finder = new Finder();
        $finder->ignoreUnreadableDirs()
            ->directories()
            ->sortByName()
            ->depth('== 0');
        
        foreach ($folders as $folder) {
            if (file_exists($folder)) {
                $finder->in($folder);
            }
        }
        
        $themes = array();
        foreach ($finder as $file) {
            /* @var $file \SplFileInfo */
            $path = str_replace(
                array(DOSSIER_ROOT, PROJECT_ROOT, getenv('HOME')), 
                array('${DOSSIER}', '${PROJECT}', '${HOME}'), 
                $file->getPathInfo()->getRealpath()
            );
            $themes[$file->getFilename()] = $path;
        }
        
        ksort($themes);
        if ($all) {
            return $themes;
        }
        
        $themesSorted = array();
        foreach ($themes as $name => $path) {
            $index = array_search($path, $folders);
            
            if (!array_key_exists($name, $themesSorted)) {
                $themesSorted[$name] = $path;
            } else {
                $existingIndex = array_search($themesSorted[$name], $folders);
                if ($index < $existingIndex) {
                    $themesSorted[$name] = $path;
                }
            }
        }
        return $themesSorted;
    }
    
    
    /**
     * Save YAML to config file 
     * 
     * @param string $targetDir
     * @param string $yaml
     * @return \Magdev\Dossier\Service\ConfigService
     */
    protected function saveYaml(string $targetDir, string $yaml): ConfigService
    {
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        file_put_contents($targetDir.'/dossier.yaml', $yaml);
        return $this;
    }
    
    
    /**
     * Load the configuration
     * 
     * @return ConfigService
     */
    protected function loadConfig(): ConfigService
    {
        clearstatcache();
        $configDirectories = array(DOSSIER_ROOT.'/app/conf', getenv('HOME').'/.dossier', PROJECT_ROOT.'/.conf');
        
        $env = getenv('APP_ENV') ?: 'prod';
        
        $cachePath = DOSSIER_CACHE.'/src/'.strtolower($env).'DossierCachedConfig.php'; 
        
        $fileLocator = new FileLocator($configDirectories);
        $loader = new ConfigLoader($fileLocator);
        $cache = new ConfigCache($cachePath, getenv('APP_DEBUG'));
        
        if (!$cache->isFresh()) {
            $resources = array();
            $configFiles = $fileLocator->locate('dossier.yaml', null, false);
            
            $envConfigFiles = $fileLocator->locate('dossier_'.strtolower($env).'.yaml', null, false);
            if (!is_array($envConfigFiles)) {
                $envConfigFiles = array($envConfigFiles);
            }
            $configFiles = array_merge($configFiles, $envConfigFiles);
            
            foreach ($configFiles as $configFile) {
                $loader->load($configFile);
                $resources[] = new FileResource($configFile);
            }
            $code = '<?php'.PHP_EOL.'return '.(string) $loader.';'.PHP_EOL;
            $cache->write($code, $resources);
        }
        
        $config = require $cachePath;
        $this->config = new Dot($config['config']);
        $this->global = new Dot($config['global']);
        return $this;
    }
    
    
    /**
     * Check if an array is an index flat array
     * 
     * @param array $array
     * @return bool
     */
    protected function isIndexedFlatArray(array $array): bool
    {
        return array_key_exists(0, $array) !== false && is_scalar($array[0]) !== false;
    }
}

