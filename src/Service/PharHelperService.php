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


use Symfony\Component\Console\Exception\RuntimeException;

/**
 * Phar helper
 * 
 * @author magdev
 */
class PharHelperService
{
    /**
     * The Phar URL if inside of an archive
     * @var string
     */
    private $pharUrl = '';
    
    /**
     * Monolog service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    
    /**
     * Constructor
     */
    public function __construct(MonologService $logger)
    {
        $this->pharUrl = \Phar::running(true);
        $this->logger = $logger;
    }
    
    
    /**
     * Check if running inside a phar-archive
     * 
     * @return bool
     */
    public function isInPhar(): bool
    {
        return $this->pharUrl != '';
    }
    
    
    /**
     * Get the full URL for a file inside a phar-archive
     * 
     * @param string $append
     * @return string
     */
    public function getPharUrl(string $append = ''): string
    {
        if ($this->isInPhar()) {
            if (!$append) {
                return $this->pharUrl;
            }
            return $this->pharUrl.'/'.$append;
        }
        
        if (!$append) {
            return DOSSIER_ROOT;
        }
        return DOSSIER_ROOT.'/'.$append;
    }
    
    
    /**
     * Read a file from phar-archive
     * 
     * @param string $file
     */
    public function read(string $file)
    {
        return file_get_contents($this->getPharUrl($file));
    }
    
    
    /**
     * Get a local temp-path for files in phar-archives
     * 
     * @param string $file
     * @return string
     */
    public function createLocalTempFile(string $file): string
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'dossier-');
        $this->copy($file, $tmpfile);
        return $tmpfile;
    }
    
    
    /**
     * Copy a file from source to local fs
     *
     * @param string $source Relative path to DOSSIER_ROOT
     * @param string $target Path on local fs
     * @return bool
     */
    public function copy(string $source, string $target): bool
    {
        return copy($this->getPharUrl($source), $target);
    }
    
    
    /**
     * Copy a directory from phar-archive to local filesystem
     * 
     * @TODO Fix recursive directory iteration
     * @param string $source
     * @param string $target
     * @return bool
     */
    public function copyDir(string $source, string $target): bool
    {
        $rit = new \RecursiveDirectoryIterator(DOSSIER_ROOT.'/'.$source);
        foreach (new \RecursiveIteratorIterator($rit) as $filename => $current) {
            if ($current->getFilename() != '.' && $current->getFilename() != '..') {
                /* @var $current \SplFileInfo */
                $relpath = str_replace(DOSSIER_ROOT.'/'.$source.'/', '', $current->getRealPath());
                
                $targetDir = dirname($target.'/'.$relpath);
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                copy($current->getRealPath(), $target.'/'.$relpath);
            }
        }
        return true;
    }
    
    
    /**
     * Extract the entire Phar archive
     * 
     * @return \Magdev\Dossier\Service\PharHelperService
     */
    public function extractArchive(string $targetDir): PharHelperService
    {
        if ($this->isInPhar()) {
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            try {
                $phar = new \Phar($_SERVER['SCRIPT_FILENAME']);
                $phar->extractTo($targetDir);
            } catch (\Exception $e) {
                throw new RuntimeException('Error while extracting archive', -1, $e);
            }
        }
        return $this;
    }
}

