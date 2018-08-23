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

/**
 * Formatter Service
 * 
 * @author magdev
 */
class FormatterService
{
    const YEAR  = 31536000;
    const MONTH = 2635200;
    const DAY   = 86400;
    
    /**
     * Translator
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator = null;
    
    
    /**
     * Constructor
     * 
     * @param \Magdev\Resume\Service\TranslatorService $translator
     */
    public function __construct(TranslatorService $translator)
    {
        $this->translator = $translator->getTranslator();
    }
    
    
    
    /**
     * Format seconds to years, months
     *
     * @TODO Implement translation
     * @param int $seconds
     * @param string $key
     * @return string
     */
    public function formatYearMonthDuration(int $seconds): string 
    {
        $null = new \DateTime('@0');
        $length = new \DateTime("@$seconds");
        
        $years = floor($seconds / self::YEAR);
        $months = floor($seconds - ($seconds * self::YEAR));
        
        $msg = $this->translator->transChoice('date.format.year', $years);
        if ($months) {
            $msg .= ', '.$this->translator->transChoice('date.format.month', $months);
        }
        return $null->diff($length)->format($msg);
    }
    
    
    /**
     * Wrapper for formatYearMonthDuration()
     * 
     * @deprecated
     * @param int $seconds
     * @return string
     */
    public function formatExperience(int $seconds): string
    {
        return $this->formatYearMonthDuration($seconds);
    }
}