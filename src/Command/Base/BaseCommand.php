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
 
namespace Magdev\Dossier\Command\Base;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;


/**
 * Base command 
 * 
 * @author magdev
 */
class BaseCommand extends Command
{
    /**
     * Configuration service
     * @var \Magdev\Dossier\Service\ConfigService
     */
    protected $config = null;
    
    /**
     * Translator service
     * @var \Magdev\Dossier\Service\TranslatorService
     */
    protected $translator = null;
    
    /**
     * Monolog Service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    /**
     * The IO-Style object
     * @var \Magdev\Dossier\Style\DossierStyle
     */
    protected $io = null;
    
    
    /**
     * Write the application header
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Magdev\Dossier\BaseCommand\Base\BaseCommand
     * @deprecated
     */
    protected function writeHeader(OutputInterface $output): BaseCommand
    {
        $this->getService('output_helper')
            ->setOutput($output)
            ->writeApplicationHeader();
        return $this;
    }
    
    
    /**
     * Get a service from the container
     * 
     * @param string $name
     * @return mixed
     */
    protected function getService(string $name)
    {
        return $this->getApplication()->getService($name);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->addOption('no-header', 'N', InputOption::VALUE_NONE, 'Suppress header <option>(format=table)</>');
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\BaseCommand\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->config = $this->getService('config');
        $this->logger = $this->getService('monolog');
        
        $locale = $this->config->get('translator.locale', 'en');
        if ($input->hasOption('locale')) {
            $locale = $input->getOption('locale');
        }
        $this->translator = $this->getService('translator')->setLocale($locale);
        
        $this->io = $this->getHelper('output')
            ->getIoStyle($input, $output);
        
        if (!$input->hasOption('no-header') || !$input->getOption('no-header')) {
            $this->io->header($this->translator->trans('app.header'));
        }
    }
    
    
    /**
     * Log and output debug messages
     * 
     * @param string $message
     * @return \Magdev\Dossier\Command\Base\BaseCommand
     */
    protected function debugLog(string $message): BaseCommand
    {
        $this->logger->debug($message);
        $this->io->debug($message);
        return $this;
    }
    
    
    /**
     * Log and output error messages
     *
     * @param string $message
     * @return \Magdev\Dossier\Command\Base\BaseCommand
     */
    protected function errorLog(string $message): BaseCommand
    {
        $this->logger->error($message);
        $this->io->error($message);
        return $this;
    }
    
    
    /**
     * Log and output warnings
     *
     * @param string $message
     * @return \Magdev\Dossier\Command\Base\BaseCommand
     */
    protected function warningLog(string $message): BaseCommand
    {
        $this->logger->warning($message);
        $this->io->warning($message);
        return $this;
    }
}

