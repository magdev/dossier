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
 
namespace Magdev\Dossier\Command\Config;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Magdev\Dossier\Service\ConfigService;
use Magdev\Dossier\Command\Base\BaseCommand;

/**
 * Show the full configuration
 * 
 * @author magdev
 */
class ConfigShowCommand extends BaseCommand
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {        
        $this->setName('config:show') 
            ->setDescription('Show or export the full configuration')
            
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format (table|yaml|json|php)', 'table')
            ->addOption('return', 'r', InputOption::VALUE_NONE,     'PHP: Include return statement')
            ->addOption('tags',   't', InputOption::VALUE_NONE,     'PHP: Include PHP tags')
            ->addOption('pretty', 'p', InputOption::VALUE_NONE,     'JSON: Output pretty printed')
            ->addOption('indent', 'i', InputOption::VALUE_OPTIONAL, 'YAML: Indentation width', 2)
            ->addOption('depth',  'd', InputOption::VALUE_OPTIONAL, 'YAML: Render depth before switching to inline YAML', 3);
            
        parent::configure();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('format') != 'table') {
            $input->setOption('no-header', true);
        }
        parent::initialize($input, $output);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $export = $this->getHelper('export');
        /* @var $export \Magdev\Dossier\Helper\ExportHelper */
        
        switch ($input->getOption('format')) {
            case 'yaml':
                $this->io->write($export->toYAML(
                    $this->config->getConfig()->all(), 
                    (int) $input->getOption('depth'),
                    (int) $input->getOption('indent')
                ));
                break;
                
            case 'json':
                $this->io->write($export->toJSON(
                    $this->config->getConfig()->all(),
                    (bool) $input->getOption('pretty')
                ));
                break;
                
            case 'php':
                $this->io->write($export->toPHP(
                    $this->config->getConfig()->all(),
                    (bool) $input->getOption('include-return'),
                    (bool) $input->getOption('include-tags')
                ));
                break;
                
            default:
                $outHelper = $this->getHelper('output');
                /* @var $outHelper \Magdev\Dossier\Helper\OutputHelper */
                
                $this->io->newLine();
                $this->io->writeln(array(
                    '  '.$this->translator->trans('info.debug_mode').': '.$this->io->debugStatus(),
                    '  '.$this->translator->trans('info.environment').': '.$this->io->environment()
                ));
                $this->io->newLine();
                $this->io->table(
                    array('Path', 'Value', 'Global'),
                    $this->config->allTable($this->io, 6)
                );
                break;
        }
    }
}

