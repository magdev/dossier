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
 
namespace Magdev\Dossier\Service;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Magdev\Dossier\Style\DossierStyle;

/**
 * Output helper
 * 
 * @author magdev
 * @deprecated 
 */
class OutputHelperService
{
    /**
     * Output object
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output = null;
    
    /**
     * Translator service
     * @var \Magdev\Dossier\Service\TranslatorService
     */
    protected $translator = null;
    
    /**
     * Output style helper
     * @var \Magdev\Dossier\Style\DossierStyle
     */
    protected $ioStyle = null;
    
    
    /**
     * Constructor
     * 
     * @param \Magdev\Dossier\Service\TranslatorService $translator
     */
    public function __construct(TranslatorService $translator)
    {
        $this->translator = $translator;
    }
    
    
    /**
     * Set the output object
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Magdev\Dossier\Service\OutputHelperService
     */
    public function setOutput(OutputInterface $output): OutputHelperService
    {
        $this->output = $output;
        return $this->addOutputStyles();
    }
    
    
    /**
     * Get the style helper for dossier
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Magdev\Dossier\Style\DossierStyle
     */
    public function getIoStyle(InputInterface $input, OutputInterface $output): DossierStyle
    {
        $this->setOutput($output);
        if (!$this->ioStyle) {
            $this->ioStyle = new DossierStyle($input, $output);
        }
        return $this->ioStyle;
    }
    
    
    /**
     * Write the application header
     * 
     * @return \Magdev\Dossier\Service\OutputHelperService
     */
    public function writeApplicationHeader(): OutputHelperService
    {
        $this->output->write('<fg=magenta;options=bold>'.base64_decode(DOSSIER_LOGO).'</>');
        $this->output->writeln('<fg=magenta;options=bold> '.$this->translator->trans('app.header').'</>');
        $this->output->writeln('');
        return $this;
    }
    
    
    /**
     * Write a configuraton value
     * 
     * @param string $key
     * @param unknown $value
     * @return OutputHelperService
     */
    public function writeConfigValue(string $key, $value): OutputHelperService
    {
        $this->output->writeln('<fg=cyan;options=bold> '.$this->padRight($key).'</>: '.'<fg=yellow;options=bold> '.$value.'</>');
        return $this;
    }
    
    
    /**
     * Format a boolean value for output
     * 
     * @param bool $status
     * @param int $width
     * @return string
     */
    public function formatBoolean(bool $status, int $width = null): string
    {
        $string = $status ? '<fg=green;options=bold>*</>' : '<fg=red;options=bold>-</>';
        if (is_int($width)) {
            $indent = floor(($width - 1) / 2);
            $string = str_repeat(' ', $indent).$string;
        }
        return $string;
    }
    
    
    /**
     * Add some output styles to the output object
     * 
     * @return \Magdev\Dossier\Service\OutputHelperService
     */
    protected function addOutputStyles(): OutputHelperService
    {
        $formatter = $this->output->getFormatter();
        $formatter->setStyle('cmd', new OutputFormatterStyle('white', 'blue', array('bold')));
        $formatter->setStyle('header', new OutputFormatterStyle('magenta', null, array('bold')));
        return $this;
    }
    
    
    /**
     * Pad a string to the right
     * 
     * @param string $text
     * @param int $width
     * @return string
     */
    protected function padRight(string $text, int $width = 50): string 
    {
        return str_pad($text, $width, ' ', STR_PAD_RIGHT);
    }
}

