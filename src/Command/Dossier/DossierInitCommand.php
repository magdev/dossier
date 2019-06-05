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
 
namespace Magdev\Dossier\Command\Dossier;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Magdev\Dossier\Command\Base\BaseCommand;

/**
 * Initialize a new project directory
 * 
 * @author magdev
 */
final class DossierInitCommand extends BaseCommand
{
    /**
     * The InitForm  
     * @var \Magdev\Dossier\Form\Extension\Form
     */
    protected $form = null; 
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('dossier:init')
            ->setDescription('Initialize a directory as dossier project')
            ->setHelp('Initialize a new project. Creates the folder structure and some example markdown files')
            ->addOption('directory', 'd', InputOption::VALUE_OPTIONAL, 'Set the output directory', getcwd());
        
        parent::configure();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        
        try {
            $this->form = $this->getHelper('form')
                ->getFormByName('form.init', $input, $output);
        } catch (FormException $fe) {
            throw new RuntimeException(get_class($fe).': '.$fe->getMessage(), $fe->getCode(), $fe);
        }
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::interact()
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->io->title($this->translator->trans('form.init.header'));
        $this->io->newLine();
        $this->form->process();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetDir = $input->getOption('directory');
        
        if ($this->isDossierProjectDirectory($targetDir)) {
            throw new RuntimeException('Directory '.$targetDir.' seems to be an existing dossier project');
        }
        
        $this->initTargetDirectory($targetDir);
        
        if ($this->form->isProcessed()) {
            
            if ($this->form->hasData('userstyle_type')) {
                $type = strtolower($this->form->getData('userstyle_type'));
                $file = $targetDir.'/.conf/'.$type.'/userstyles.'.$type;
                
                if (!is_dir(dirname($file))) {
                    mkdir(dirname($file), 0755, true);
                }
                file_put_contents($file, '');
            }
            
            $formatter = $this->getHelper('formatter');
            
            $output->writeln($formatter->formatBlock(array(
                ' $ dossier.phar create:person',
                ' $ dossier.phar create:intro',
                ' $ dossier.phar create:cv',
                ' $ dossier.phar create:project',
            ), 'cmd', true));
        }
    }
    
    
    /**
     * Check if the path is a valid dossier project
     * 
     * @param string $path
     * @return bool
     */
    private function isDossierProjectDirectory(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }
        if (!is_dir($path.'/cv')) {
            return false;
        }
        if (!is_dir($path.'/certs')) {
            return false;
        }
        if (!is_dir($path.'/.conf')) {
            return false;
        }
        if (!file_exists($path.'/.conf/dossier.yaml')) {
            return false;
        }
        if (!file_exists($path.'/.env')) {
            return false;
        }
        if (!file_exists($path.'/intro.md')) {
            return false;
        }
        if (!file_exists($path.'/person.md')) {
            return false;
        }
        if (!file_exists($path.'/letter.md')) {
            return false;
        }
        return true;
    }
    
    
    
    /**
     * Create the directory structure
     * 
     * @return string
     */
    private function initTargetDirectory(string $dir): string
    {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true) === false) {
                throw new RuntimeException('Creating directory '.$dir.' failed');
            }
        }
        if (!is_dir($dir.'/cv')) {
            mkdir($dir.'/cv', 0755, true);
        }
        if (!is_dir($dir.'/certs/pdf')) {
            mkdir($dir.'/certs/pdf', 0755, true);
        }
        if (!is_dir($dir.'/certs/png')) {
            mkdir($dir.'/certs/png', 0755, true);
        }
        if (!is_dir($dir.'/.conf')) {
            mkdir($dir.'/.conf', 0755, true);
        }
        file_put_contents($dir.'/.conf/dossier.yaml', '');
        file_put_contents($dir.'/.env', 'APP_DEBUG=false'.PHP_EOL.'APP_ENV=prod'.PHP_EOL);
        return $dir;
    }
    
}

