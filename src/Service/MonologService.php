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

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;

/**
 * Log service
 * 
 * @author magdev
 */
class MonologService
{
    /**
     * Configuration service
     * @var \Magdev\Dossier\Service\ConfigService
     */
    protected $config = null;
    
    /**
     * Monolog logger
     * @var \Monolog\Logger
     */
    protected $logger = null;
    
    
    /**
     * Constructor
     * 
     * @param \Magdev\Dossier\Service\ConfigService $config
     */
    public function __construct(ConfigService $config)
    {
        $this->config = $config;
        
        $logLevel = getenv('APP_DEBUG') ? Logger::DEBUG : $config->get('monolog.log_level', Logger::INFO);
        
        $this->logger = new Logger('dossier');
        $this->logger->pushHandler(new StreamHandler(PROJECT_ROOT.'/.dossier.log', $logLevel))
            ->pushProcessor(new IntrospectionProcessor(
                $logLevel, 
                $config->get('monolog.skip_class_partials', array())
            ));
    }
    
    
    /**
     * Get the Logger object
     * 
     * @return \Monolog\Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }
    
    
    /**
     * Delegate method calls to internal Logger
     *
     * @param string $method
     * @param array $args
     * @throws \BadFunctionCallException
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (!method_exists($this->logger, $method)) {
            throw new \BadFunctionCallException('Unknown method: '.$method);
        }
        return call_user_func_array(array($this->logger, $method), $args);
    }
}

