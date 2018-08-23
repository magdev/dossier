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

use Magdev\Dossier\Analyzer\Base\AnalyzableInterface;
use Magdev\Dossier\Analyzer\Base\AnalyzerInterface;
use Magdev\Dossier\Analyzer\Base\AnalyzerException;

/**
 * Service to analyze models
 * 
 * @author magdev
 */
class AnalyzerService
{
    /**
     * Configuration service
     * @var \Magdev\Dossier\Service\AnalyzerService
     */
    protected $config = null;
    
    /**
     * Monolog service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    /**
     * Analyzer objects
     * @var \ArrayObject
     */
    protected $analyzers = null;
    
    
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
        $this->analyzers = new \ArrayObject();
    }
    
    
    /**
     * Get all analyzers
     * 
     * @return \ArrayObject
     */
    public function getAnalyzers(): \ArrayObject
    {
        return $this->analyzers;
    }
    
    
    /**
     * Add an analyzer
     * 
     * @param \Magdev\Dossier\Analyzer\Base\AnalyzerInterface $analyzer
     * @return \Magdev\Dossier\Service\AnalyzerService
     * @throws \Magdev\Dossier\Analyzer\Base\AnalyzerException
     */
    public function addAnalyzer(AnalyzerInterface $analyzer): AnalyzerService
    {
        if ($this->hasAnalyzer($analyzer->getName())) {
            throw new AnalyzerException('Analyzer with name '.$name.' already exists');
        }
        $this->analyzers->append($analyzer);
        $this->logger->debug('Analyzer '.get_class($analyzer).' added with name '.$analyzer->getName());
        return $this;
    }
    
    
    /**
     * Check if an analyzer name is already taken
     * 
     * @param string $name
     * @return bool
     */
    public function hasAnalyzer(string $name): bool
    {
        return $this->getAnalyzers() instanceof AnalyzableInterface;
    }
    
    
    /**
     * Get an analyzer by name
     *
     * @param string $name
     * @return \Magdev\Dossier\Analyzer\Base\AnalyzerInterface
     */
    public function getAnalyzer(string $name): ?AnalyzerInterface
    {
        foreach ($this->analyzers as $a) {
            if ($a->getName() == $name) {
                return $a;
            }
        }
        return null;
    }
    
    
    /**
     * Set analyzers
     * 
     * @param \ArrayObject $analyzers
     * @return AnalyzerService
     */
    public function setAnalyzers(\ArrayObject $analyzers): AnalyzerService
    {
        $this->analyzers = $analyzers;
        return $this;
    }
}

