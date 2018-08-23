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
 
namespace Magdev\Dossier\Style;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Helper;

/**
 * Dossier console output styles
 * 
 * @author magdev
 */
class DossierStyle extends SymfonyStyle
{
    const ALIGN_LEFT   = 1;
    const ALIGN_RIGHT  = 2;
    const ALIGN_CENTER = 3;
    
    private $appName = '';
    private $appVersion = '';
    

    public function setAppName(string $appName): DossierStyle
    {
        $this->appName = $appName;
        return $this;
    }



    public function setAppVersion(string $appVersion): DossierStyle
    {
        $this->appVersion = $appVersion;
        return $this;
    }



    /**
     * Print as command-block
     * 
     * @param strng|array $message
     * @return \Magdev\Dossier\Style\DossierStyle
     */
    public function cmd($message): DossierStyle
    {
        $this->block($message, null, 'cmd');
        $this->writeln($this->getFormatter()->formatBlock($message, 'cmd', true));
        return $this;
    }
    
    
    /**
     * Print the application header
     * 
     * @param string|array $message
     * @return \Magdev\Dossier\Style\DossierStyle
     */
    public function header($message): DossierStyle
    {
        switch (getenv('DOSSIER_HEADER_STYLE')) {
            case 'none':
                break;
                
            case 'logo-only';
                $this->text('<lightmagenta>'.base64_decode(DOSSIER_LOGO).'</>');
                break;
                
            case 'text-only':
                if ($this->appName && $this->appVersion) {
                    $this->text('<lightmagenta>'.sprintf('%s %s', $this->appName, $this->appVersion).'</>');
                }
                $this->text('<lightmagenta>'.$message.'</>');
                break;
                
            default:
            case 'full':
                $this->text('<lightmagenta>'.base64_decode(DOSSIER_LOGO).'</>');
                if ($this->appName && $this->appVersion) {
                    $this->text('<lightmagenta>'.sprintf('%s %s', $this->appName, $this->appVersion).'</>');
                }
                $this->text('<lightmagenta>'.$message.'</>');
                break;
        }
        $this->newLine();
        return $this;
    }
    
    
    public function info($message)
    {
        $this->text('<lightgreen> INFO: '.$message.'</>');
    }
    
    
    /**
     * Print info messages
     * 
     * @param string|array $message
     */
    public function result($message)
    {
        $this->block($message, 'RESULT', 'fg=black;bg=cyan', ' ', true);
    }
    
    
    /**
     * Print debug message
     *
     * @param string|array $message
     */
    public function debug($message)
    {
        if (getenv('APP_DEBUG')) {
            $this->text('<lightyellow> DEBUG: '.$message.'</>');
        }
    }
    
    
    /**
     * Get a message with a fixed length with specified alignment
     * 
     * @param string $message
     * @param int $length
     * @param int $alignment
     * @return string
     */
    public function align(string $message, int $length = null, int $alignment = self::ALIGN_LEFT): string
    {
        $plainLength = Helper::strlenWithoutDecoration($this->getFormatter(), $message);
        $length = $length + (strlen($message) - $plainLength);
        
        switch ($alignment) {
            case self::ALIGN_CENTER:
                return str_pad($message, $length, ' ', STR_PAD_BOTH);
                
            case self::ALIGN_RIGHT:
                return str_pad($message, $length, ' ', STR_PAD_LEFT);
                
            default:
            case self::ALIGN_LEFT:
                return str_pad($message, $length, ' ', STR_PAD_RIGHT);
        }
    }
    
    
    /**
     * Format percent
     * @param int $value
     * @param array $thresholds
     * @return string
     */
    public function percent(int $value, array $thresholds = array(), string $symbol = '%'): string
    {
        $prefix = function(int $percent) {
            if ($percent < 10) { return '  '; }
            if ($percent < 100) { return ' '; }
            return '';
        };
        
        if (sizeof($thresholds)) {
            uasort($thresholds, function($a, $b) {
                if ($a['min'] == $b['min']) {
                    return 0;
                }
                return $a['min'] < $b['min'] ? -1 : 1;
            });
            
            if ($value >= end($thresholds)['max']) {
                return $prefix($value).'<lightgreen>'.$value.$symbol.'</>';
            } else if ($value <= reset($thresholds)['min']) {
                return $prefix($value).'<lightred>'.$value.$symbol.'%</>';
            }
            
            foreach ($thresholds as $threshold) {
                if ($value <= $threshold['max'] && $value >= $threshold['min']) {
                    if (isset($threshold['color'])) {
                        return $prefix($value).'<'.$threshold['color'].'>'.$value.$symbol.'</>';
                    } else {
                        return $prefix($value).$value.$symbol;
                    }
                }
            }
        }
        return $prefix($value).$value.$symbol;
    }
    
    
    /**
     * Print a colored enviroment tag
     *
     * @return string
     */
    public function environment(): string
    {
        $env = strtoupper(getenv('APP_ENV'));
        switch ($env) {
            case 'TEST':
                return '<lightyellow>'.$env.'</>';
                
            case 'DEV':
                return '<lightred>'.$env.'</>';
                
            default:
            case 'PROD':
                return '<lightgreen>'.$env.'</>';
        }
    }
    
    
    /**
     * Print the colored debug status
     * 
     * @return string
     */
    public function debugStatus(): string
    {
        return getenv('APP_DEBUG') ? '<lightred>YES</>' : '<lightgreen>NO</>';
    }
    
    
    /**
     * Get the string for a boolean value in table-rows
     * 
     * @param bool $value
     * @param string $trueChar
     * @param string $falseChar
     * @return string
     */
    public function bool(bool $value, string $trueChar = '*', string $falseChar = '-'): string
    {
        return $value == true ? $this->true($trueChar) : $this->false($falseChar);
    }
    
    
    /**
     * Get the string to mark table-rows as true
     * 
     * @param string $char
     * @return string
     */
    public function true(string $char = '*'): string
    {
        return '<lightgreen>'.$char.'</>';
    }
    
    
    /**
     * Get the string to mark table rows as false
     *
     * @param string $char
     * @return string
     */
    public function false(string $char = '-'): string
    {
        return '<lightred>'.$char.'</>';
    }
}

