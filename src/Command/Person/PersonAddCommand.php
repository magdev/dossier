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
 
namespace Magdev\Dossier\Command\Person;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Droath\ConsoleForm\Exception\FormException;
use Magdev\Dossier\Command\Base\BaseCommand;

final class PersonAddCommand extends BaseCommand
{
    /**
     * The IntroForm 
     * @var \Magdev\Dossier\Form\Extension\Form
     */
    protected $form = null;
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('person:add')
            ->setDescription('Write the Person page')
            ->addOption('review', 'r', InputOption::VALUE_NONE, 'Review file in editor');
        
        parent::configure();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Command\BaseCommand::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        
        try {
            $this->form = $this->getHelper('form')
                ->getFormByName('form.person', $input, $output);
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
        $this->io->title($this->translator->trans('form.person.header.add'));
        $this->io->newLine();
        $this->form->process();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->form->isProcessed()) {
            $markdown = $this->getService('markdown');
            /* @var $markdown \Magdev\Dossier\Service\MarkdownService */
            
            try {
                $text = $this->form->stripData('text', '');
                
                $markdown->save(PROJECT_ROOT.'/person.md', $this->form->getResults(), $text, false);
                $this->io->success($this->translator->trans('message.write.success', array(
                    '%name%' => 'Person'
                )));
                
                if ($input->getOption('review') != false) {
                    $this->getService('uri_helper')->openFileInEditor(PROJECT_ROOT.'/person.md');
                }
            } catch (FormException $fe) {
                throw new RuntimeException(get_class($fe).': '.$fe->getMessage(), $fe->getCode(), $fe);
            }
        }
    }
}

