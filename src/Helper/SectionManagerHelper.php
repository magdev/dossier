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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Magdev\Dossier\Service\ConfigService;

/**
 * Section manager
 * 
 * @author magdev
 */
class SectionManagerHelper  extends Helper
{
    /**
     * Configuration service
     * @var \Magdev\Dossier\Service\ConfigService
     */
    protected $config = null;
    
    
    /**
     * Constructor
     * 
     * @param \Magdev\Dossier\Service\ConfigService $config
     */
    public function __construct(ConfigService $config)
    {
        $this->config = $config;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Helper\HelperInterface::getName()
     */
    public function getName()
    {
        return 'section_manager';
    }
    
    
    /**
     * Get an array with section visibility settings
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return array
     */
    public function getDisabledSections(InputInterface $input): array
    {
        return array(
            'cover'    => (bool) $input->getOption('no-cover')    || $this->config->get('disable.cover',    false),
            'intro'    => (bool) $input->getOption('no-intro')    || $this->config->get('disable.intro',    false),
            'resume'   => (bool) $input->getOption('no-resume')   || $this->config->get('disable.resume',   false),
            'cv'       => (bool) $input->getOption('no-cv')       || $this->config->get('disable.cv',       false),
            'certs'    => (bool) $input->getOption('no-certs')    || $this->config->get('disable.certs',    false),
            'quotes'   => (bool) $input->getOption('no-quotes')   || $this->config->get('disable.quotes',   false),
            'toc'      => (bool) $input->getOption('no-toc')      || $this->config->get('disable.toc',      false),
            'projects' => (bool) $input->getOption('no-projects') || $this->config->get('disable.projects', false),
        );
    }
}

