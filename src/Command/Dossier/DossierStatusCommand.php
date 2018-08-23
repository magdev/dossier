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

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableSeparator;
use Magdev\Dossier\Model\Person;
use Magdev\Dossier\Model\Intro;
use Magdev\Dossier\Model\CurriculumVitae\Entry;
use Magdev\Dossier\Model\Letter;
use Magdev\Dossier\Style\DossierStyle;
use Magdev\Dossier\Command\Base\BaseCommand;

/**
 * Command to show the current status of your dossier
 * 
 * @author magdev
 */
class DossierStatusCommand extends BaseCommand
{
    /**
     * Markdown service
     * @var \Magdev\Dossier\Service\MarkdownService
     */
    private $markdown = null;
    
    private $models = array(
        'Person' => 'person.md',
        'Intro' => 'intro.md',
        'Letter' => 'letter.md'
    );
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('dossier:status')
            ->setDescription('Get the status of the current dossier files');
        
        parent::configure();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $markdown = $this->getService('markdown');
        /* @var $markdown \Magdev\Dossier\Service\MarkdownService */
        
        $analyzer = $this->getService('analyzer')->getAnalyzer('model.status');
        /* @var $analyzer \Magdev\Dossier\Analyzer\DossierStatusAnalyzer */
        
        $thresholds = $this->config->get('style.dossier_status_thresholds');
        
        $person = new Person($markdown->getDocument(PROJECT_ROOT.'/person.md'));
        $intro = new Intro($markdown->getDocument(PROJECT_ROOT.'/intro.md'));
        
        $status = array();
        $status[] = array(get_class($person), 'person.md',
            $this->io->align('---', 9, DossierStyle::ALIGN_CENTER),
            $this->io->align($this->io->percent($analyzer->analyze($person), $thresholds), 6, DossierStyle::ALIGN_RIGHT)
        );
        $status[] = array(get_class($intro), 'intro.md',
            $this->io->align('---', 9, DossierStyle::ALIGN_CENTER),
            $this->io->align($this->io->percent($analyzer->analyze($intro), $thresholds), 6, DossierStyle::ALIGN_RIGHT)
        );
        $status[] = new TableSeparator();
        
        $files = new \FilesystemIterator(PROJECT_ROOT.'/cv');
        foreach ($files as $file) {
            /* @var $file \SplFileInfo */
            $document = $markdown->getDocument($file->getPathname());
            $entry = new Entry($document);
            $status[] = array(get_class($entry), 'cv/'.$file->getFilename(), 
                $this->io->align($this->io->bool($entry->useInResume()), 9, DossierStyle::ALIGN_CENTER), 
                $this->io->align($this->io->percent($analyzer->analyze($entry), $thresholds), 6, DossierStyle::ALIGN_RIGHT)
            );
        }
        
        $this->io->table(array('Model', 'File', 
            $this->io->align('In Resume', 9), 
            $this->io->align('Status', 6, DossierStyle::ALIGN_RIGHT)
        ), $status);
    }
}

