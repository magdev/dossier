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

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magdev\Dossier\Model\CurriculumVitae;
use Magdev\Dossier\Model\Person;
use Magdev\Dossier\Model\Intro;
use Magdev\Dossier\Model\Letter;
use Magdev\Dossier\Command\Base\BaseCommand;
use Magdev\Dossier\Util\DataCollector;

/**
 * Generate your dossier
 *
 * @author magdev
 */
final class DossierBuildCommand extends BaseCommand
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('dossier:build')
            ->setDescription('Create a dossier from a set of Markdown files')
            
            ->addOption('review', 'r', InputOption::VALUE_NONE, 'Review created file')
            ->addOption('pdf', 'p', InputOption::VALUE_NONE, 'Convert to PDF (requires PDFShift API-Key)')
            ->addOption('locale', 'l', InputOption::VALUE_OPTIONAL, 'Set the locale', 'de')
            ->addOption('sort', 's', InputOption::VALUE_OPTIONAL, 'Set the sort direction for the CV', CurriculumVitae::SORT_DESC)
            ->addOption('theme', 't', InputOption::VALUE_OPTIONAL, 'Select the theme', 'print')
            ->addOption('docname', 'd', InputOption::VALUE_OPTIONAL, 'Set the name for the output document (w/o extension)', 'dossier')
            
            ->addOption('no-cover', null, InputOption::VALUE_NONE, 'Suppress the cover')
            ->addOption('no-intro', null, InputOption::VALUE_NONE, 'Suppress the introduction')
            ->addOption('no-resume', null, InputOption::VALUE_NONE, 'Suppress the resume')
            ->addOption('no-cv', null, InputOption::VALUE_NONE, 'Suppress the curriculum vitae')
            ->addOption('no-projects', null, InputOption::VALUE_NONE, 'Suppress the projects page')
            ->addOption('no-certs', null, InputOption::VALUE_NONE, 'Suppress the certificates')
            ->addOption('no-toc', null, InputOption::VALUE_NONE, 'Suppress the table of contents')
            ->addOption('no-quotes', null, InputOption::VALUE_NONE, 'Suppress the quotes');

        parent::configure();
    }


    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('theme') == 'server') {
            $this->warningLog('Template server cannot be used to render dossiers, using print theme');
            $input->setOption('theme', 'print');
        }
        
        $uriHelper = $this->getService('uri_helper');
        /* @var $uriHelper \Magdev\Dossier\Service\UriHelperService */
        
        $tpl = $this->getService('template')->setTheme($input->getOption('theme'));
        /* @var $tpl \Magdev\Dossier\Service\TemplateService */
        
        $cssproc = $this->getService('cssproc');
        /* @var $cssroc \Magdev\Dossier\Service\StylesheetProcessorService */
        
        try {
            $data = new DataCollector(array(
                'disabled' => $this->getHelper('section_manager')->getDisabledSections($input),
                'theme' => $input->getOption('theme'),
                'name' => $input->getOption('docname'),
                'tags' => $this->config->get('cv.tags'),
                'locale' => $this->translator->getTranslator()->getLocale(),
                'stylesheet' => $cssproc->parseThemeStyles(),
                'userstyles' => $cssproc->parseUserstyles(),
                'favicon' => $uriHelper->getDataUriFromFile($tpl->getThemeFile('favicon.ico')),
            ));
            
            $models = $this->getService('markdown')->getFileSet($input->getOption('sort'));
            $data->merge($models);
    
            $outputFile = $tpl->render('document.html.twig', $data, PROJECT_ROOT.'/_output');
            if ($input->getOption('pdf')) {
                $outputFile = $this->getService('pdf')->createPdf($outputFile);
            }
    
            $this->io->result($this->translator->trans('message.output_file').': '.$uriHelper->getRelativePath($outputFile));
            $this->io->newLine();
    
            $this->io->success($this->translator->trans('message.build.success'));
            $this->io->newLine();
            
            if ($input->getOption('review')) {
                $uriHelper->openFileInEditor($outputFile);
            }
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }
}
