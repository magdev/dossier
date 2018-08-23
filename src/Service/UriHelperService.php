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
 * URI helper
 * 
 * @author magdev
 */
class UriHelperService
{
    /**
     * Root directory to strip to generate relative URLs
     * @var string
     */
    protected $rootDir = '';
    
    /**
     * Phar Helper service
     * @var \Magdev\Dossier\Service\PharHelperService
     */
    protected $pharHelper = null;
    
    /**
     * Monolog service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    
    /**
     * Constructor
     * 
     * @param \Magdev\Dossier\Service\PharHelperService $helper
     */
    public function __construct(MonologService $logger, PharHelperService $helper)
    {
        $this->logger = $logger;
        $this->pharHelper = $helper;
        $this->rootDir = PROJECT_ROOT.'/';
    }
    
    
    /**
     * Set the root directory
     * 
     * @param string $dir
     * @return \Magdev\Dossier\Service\UrlHelperService
     */
    public function setRootDirectory(string $dir): UrlHelperService
    {
        $this->rootDir = $dir;
        return $this;
    }
    
    
    /**
     * Get DataURI from file
     * 
     * @param string $file
     * @param string $mimetype
     * @return string
     */
    public function getDataUriFromFile(string $file, $mimetype = null): string
    {
        $tmpfile = null;
        if ($this->pharHelper->isInPhar()) {
            $tmpfile = $this->pharHelper->createLocalTempFile($file);
            $f = new \SplFileObject($tmpfile);
        } else {
            $f = new \SplFileObject($file);
        }
        
        if (!$mimetype) {
            $fi = new \finfo();
            $mimetype = $fi->file($f->getRealPath(), FILEINFO_MIME);
        }
        $data = file_get_contents($f->getRealPath());
        
        if ($tmpfile && file_exists($tmpfile)) {
            @unlink($tmpfile);
        }
        
        return $this->toDataUri($data, $mimetype);
    }
    
    
    /**
     * Get a DataURI from data
     * 
     * @param mixed $data
     * @param string $mimetype
     * @return string
     */
    public function getDataUriFromData($data, $mimetype = 'text/plain'): string
    {
        return $this->toDataUri($data, $mimetype);
    }
    
    
    /**
     * Open a file in the default editor
     * 
     * @param string $file
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     * @return \Magdev\Dossier\Service\UriHelperService
     */
    public function openFileInEditor(string $file): UriHelperService
    {
        if (getenv('XSESSION_IS_UP') == 'yes') {
            system('xdg-open '.$file);
        } else if ($editor = getenv('EDITOR')) {
            system($editor.' '.$file);
        } else {
            throw new RuntimeException('Cannot find a responsible editor');
        }
        return $this;
    }
    
    
    /**
     * Get the relative path for project files
     * 
     * @param string $realpath
     * @return string
     */
    public function getRelativePath(string $realpath): string
    {
        return str_replace(PROJECT_ROOT.'/', '', $realpath);
    }
    
    /**
     * Format the DataURI
     * 
     * @param mixed $data
     * @param string $mimetype
     * @return string
     */
    protected function toDataUri($data, string $mimetype): string
    {
        return 'data:'.$mimetype.'; base64,'.base64_encode($data);
    }
}

