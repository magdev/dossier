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

class GitService
{
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
     * System service
     * @var \Magdev\Dossier\Service\SystemService
     */
    protected $system = null;
    
    
    /**
     * Constructor
     *
     * @param \Magdev\Dossier\Service\ConfigService $config
     * @param \Magdev\Dossier\Service\MonologService $logger
     * @param \Magdev\Dossier\Service\SystemService $system
     */
    public function __construct(ConfigService $config, MonologService $logger, SystemService $system)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->system = $system;
    }
    
    
    /**
     * Initialize a new repository
     * 
     * @return bool
     */
    public function init(): bool
    {
        if (!$this->isGitRepository()) {
            $this->system->exec('git init');
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
        return $this->system->exec('git branch | grep \\\* | cut -d \' \' -f2', SystemService::MODE_LASTLINE);
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
}
