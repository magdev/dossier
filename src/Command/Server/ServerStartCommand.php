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
 
namespace Magdev\Dossier\Command\Server;

use Magdev\Dossier\Command\Base\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Magdev\Dossier\Util\DataCollector;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * Start the local HTTP server 
 * 
 * @author magdev
 */
class ServerStartCommand extends BaseCommand
{
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Command\Base\BaseCommand::configure()
     */
    public function configure()
    {
        $this->setName('server:start')
            ->setDescription('Start a local HTTP server')
            ->addOption('socket', 's', InputOption::VALUE_OPTIONAL, 'Set the HTTP socket (host, port or host:port)', 'localhost:8000')
            ->addOption('no-index', null, InputOption::VALUE_NONE, 'Suppress rendering of index.html before starting the server');
        
        parent::configure();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $socket = $input->getOption('socket');
        $docroot = PROJECT_ROOT.'/_output';
        
        if (!$input->getOption('no-index')) {
            $this->createIndex($docroot);
        }
        
        $cmd = sprintf('/usr/bin/env php -S %s -t %s', $socket, $docroot);
        try {
            $process = new Process($cmd);
            $process->setTimeout((60*60*24*365))
                ->start();
            
            $this->io->info(sprintf('Starting HTTP server on %s, press CTRL+C to stop', $socket));
            $process->wait(function($type, $buffer) {
                if (getenv('APP_DEBUG')) {
                    $this->io->write('  <lightyellow>[GET] <fg=cyan>'.$buffer.'</>');
                }
            });
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());
        }
    }
    
    
    /**
     * Create an index.html file before server start
     * 
     * @param string $docroot
     * @return void
     */
    protected function createIndex(string $docroot): void
    {
        if (file_exists($docroot.'/index.html')) {
            unlink($docroot.'/index.html');
        }
        
        try {
            $tpl = $this->getService('template')->setTheme('server');
            /* @var $tpl \Magdev\Dossier\Service\TemplateService */
            
            $cssproc = $this->getService('cssproc');
            /* @var $cssroc \Magdev\Dossier\Service\StylesheetProcessorService */
            
            $uriHelper = $this->getService('uri_helper');
            /* @var $uriHelper \Magdev\Dossier\Service\UriHelperService */
            
            $data = new DataCollector(array(
                'locale' => $this->translator->getTranslator()->getLocale(),
                'stylesheet' => $cssproc->parseThemeStyles(),
                'favicon' => $uriHelper->getDataUriFromFile($tpl->getThemeFile('favicon.ico')),
            ));
            
            $files = array();
            $fs = new \FilesystemIterator($docroot);
            foreach ($fs as $file) {
                if (!sizeof($files)) {
                    $data->setData('first_link', $file);
                }
                $files[] = $file;
            }
            $data->setData('links', $files);
            
            $html = $tpl->renderDocument('index.html.twig', $data);
            if (false === file_put_contents($docroot.'/index.html', $html)) {
                throw new RuntimeException('Error while writing index.html file');
            }
            $this->debugLog(sprintf('File index.html created with %s bytes', mb_strlen($html)));
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }
}

