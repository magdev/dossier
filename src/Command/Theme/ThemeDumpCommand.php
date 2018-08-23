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
 
namespace Magdev\Dossier\Command\Theme;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Magdev\Dossier\Command\Base\BaseCommand;

/**
 * Create a local copy of a template
 *
 * @author magdev
 */
final class ThemeDumpCommand extends BaseCommand
{
    /**
     * Phar helper service
     * @var \Magdev\Dossier\Service\PharHelperService
     */
    private $pharHelper = null;
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('theme:dump')
            ->setDescription('Create a local copy of a theme')
            
            ->addArgument('theme', InputArgument::REQUIRED, 'The name of the theme')
            ->addOption('locale', 'l', InputOption::VALUE_OPTIONAL, 'Set the locale', 'de')
            ->addOption('rename', 'r', InputOption::VALUE_OPTIONAL, 'Rename the theme', '')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output folder', getenv('HOME').'/.dossier/tpl');
        
        parent::configure();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->pharHelper = $this->getService('phar_helper');
        
        parent::initialize($input, $output);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $theme = $input->getArgument('theme');
        $outDir = $input->getOption('output');
        $newName = $input->getOption('rename');
        
        $themeDir = 'app/tpl/'.$theme;
        $targetDir = !$newName ? $outDir.'/'.$theme : $outDir.'/'.$newName;
        $this->pharHelper->copyDir($themeDir, $targetDir);
        
        $this->io->success($this->translator->trans('message.dump.success', array('%theme%' => $theme)));
        /*
        if ($outDir != getenv('HOME').'/.dossier/tpl') {
            $output->writeln('<fg=cyan> '.$this->translator->trans('message.export.template_dir').'</>');
            $output->writeln('<fg=blue> '.$this->translator->trans('message.export.code.template_environment', array('%path%' => $outDir)).'</>');
        } else {
            $output->writeln('<fg=cyan> '.$this->translator->trans('message.export.template_homedir').'</>');
        }*/
        $this->io->newLine();
    }
}

