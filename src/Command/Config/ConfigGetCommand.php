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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\Table;
use Magdev\Dossier\Command\Base\BaseCommand;

/**
 * Get a configuration value
 * 
 * @author magdev
 */
class ConfigGetCommand extends BaseCommand
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('config:get')
            ->setDescription('Get a configuration value')
            ->addArgument('path', InputArgument::REQUIRED, 'Configuration path')
            
            ->addOption('format',         'f', InputOption::VALUE_OPTIONAL, 'Output format (table|yaml|json|php)', 'table')
            ->addOption('plain',          'p', InputOption::VALUE_NONE,     'Show the value only <option>(implies --no-header)</>')
            
            ->addOption('include-return', 'R', InputOption::VALUE_NONE,     'Include return statement <option>(format=php)</>')
            ->addOption('include-tags',   'T', InputOption::VALUE_NONE,     'Include PHP tags <option>(format=php)</>')
            
            ->addOption('pretty',         'P', InputOption::VALUE_NONE,     'JSON pretty print <option>(format=json)</>')
            
            ->addOption('indent',         'i', InputOption::VALUE_OPTIONAL, 'Indentation width <option>(format=yaml)</>', 2)
            ->addOption('depth',          'd', InputOption::VALUE_OPTIONAL, 'Render depth before switching to inline YAML <option>(format=yaml)</>', 3);
    
        parent::configure();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $input->setOption('no-header', $input->getOption('plain'));
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
        
        $path = $input->getArgument('path');
        $value = $this->config->get($path);
        
        if ($input->getOption('plain')) {
            $this->io->write($value);
            exit;
        }
        
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
                $global = $this->io->bool($this->config->isGlobalConfig($path, $value));
                $this->io->writeln(array(
                '  '.$this->translator->trans('info.debug_mode').': '.$this->io->debugStatus(),
                '  '.$this->translator->trans('info.environment').': '.$this->io->environment()
                ));
                $this->io->newLine();
                $this->io->table(
                    array('Path', 'Value', 'Global'),
                    array(array($path, $value, $this->io->align($global, 6)))
                );
                break;
        }
    }
}

