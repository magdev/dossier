<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2019 magdev
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
 * @copyright 2019 Marco Grätsch
 * @package   magdev/dossier
 * @license   http://opensource.org/licenses/MIT MIT License
 */
 
namespace Magdev\Dossier\Service;

// git branch | grep \* | cut -d ' ' -f2

class GitService
{
    const MODE_OUTPUT = 1;
    const MODE_RETURN = 2;
    const MODE_LASTLINE = 3;
    
    /**
     * Configuration service
     * @var \Magdev\Dossier\Service\ConfigService
     */
    protected $config = null;
    
    /**
     * Monolog service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    
    /**
     * Constructor
     *
     * @param \Magdev\Dossier\Service\ConfigService $config
     * @param \Magdev\Dossier\Service\MonologService $logger
     */
    public function __construct(ConfigService $config, MonologService $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }
    
    
    /**
     * Initialize a new repository
     * 
     * @return bool
     */
    public function init(): bool
    {
        if (!$this->isGitRepository()) {
            $this->exec('git init');
        }
        return $this->isGitRepository();
    }
    
    
    /**
     * Get the current branch name
     * 
     * @return string
     */
    public function getCurrentBranchName(): string
    {
        if (!$this->isGitRepository()) {
            return $this->config->get('output.docname');
        }
        return $this->exec('git branch | grep \\\* | cut -d \' \' -f2', self::MODE_LASTLINE);
    }
    
    
    /**
     * Check if repository is initialized
     * 
     * @return bool
     */
    public function isGitRepository(): bool
    {
        return is_dir(PROJECT_ROOT.'/.git') && file_exists(PROJECT_ROOT.'/.git/HEAD');
    }
    
    
    /**
     * Execute a system command
     * 
     * @param string $cmd
     * @param int $mode
     * @param array $output
     * @param int $return
     * @return string|int|array
     */
    protected function exec(string $cmd, int $mode = self::MODE_OUTPUT, &$output = array(), int &$return = 0)
    {
        $lastline = exec($cmd, $output, $return);
        switch ($mode) {
            case self::MODE_LASTLINE:   return $lastline;
            case self::MODE_RETURN:     return $return;
            default:
            case self::MODE_OUTPUT:     return $output;
        }
    }
}
