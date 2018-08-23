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
 
namespace Magdev\Dossier\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magdev\Dossier\Style\DossierStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class OutputHelper extends Helper
{
    /**
     * IO style object
     * @var \Magdev\Dossier\Style\DossierStyle
     */
    private $io = null;
    
    private $appName = '';
    private $appVersion = '';
    
    public function __construct(string $appName = '', string $appVersion = '')
    {
        $this->appName = $appName;
        $this->appVersion = $appVersion;
    }
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Helper\HelperInterface::getName()
     */
    public function getName()
    {
        return 'output';
    }
    
    
    /**
     * Get the style helper for dossier
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Magdev\Dossier\Style\DossierStyle
     */
    public function getIoStyle(InputInterface $input, OutputInterface $output): DossierStyle
    {
        if (!$this->io) {
            $this->addOutputStyles($input, $output);
            
            $this->io = new DossierStyle($input, $output);
            $this->io->setAppName($this->appName)
                ->setAppVersion($this->appVersion);
        }
        return $this->io;
    }
    
    
    /**
     * Add some output styles to the output object
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Magdev\Dossier\Helper\OutputHelper
     */
    protected function addOutputStyles(InputInterface $input, OutputInterface $output): OutputHelper
    {
        $formatter = $output->getFormatter();
        $formatter->setStyle('cmd', new OutputFormatterStyle('white', 'blue', array('bold')));
        $formatter->setStyle('option', new OutputFormatterStyle('cyan', null, array('bold')));
        
        $formatter->setStyle('lightmagenta', new OutputFormatterStyle('magenta', null, array('bold')));
        $formatter->setStyle('lightgreen', new OutputFormatterStyle('green', null, array('bold')));
        $formatter->setStyle('lightred', new OutputFormatterStyle('red', null, array('bold')));
        $formatter->setStyle('lightyellow', new OutputFormatterStyle('yellow', null, array('bold')));
        return $this;
    }
}

