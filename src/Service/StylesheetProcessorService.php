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

use Leafo\ScssPhp\Compiler;

/**
 * LESS CSS processor service
 * 
 * @author magdev
 */
class StylesheetProcessorService
{
    const DELIM_MIXINS = 'MIXINS';
    const DELIM_VARS = 'VARS';
    
    const TYPE_LESS = 'less';
    const TYPE_SCSS = 'scss';
    
    /**
     * LESS parser
     * @var \Less_Parser
     */
    protected $lessc = null;
    
    /**
     * SCSS compiler
     * @var \Leafo\ScssPhp\Compiler
     */
    protected $scssc = null;
    
    /**
     * Monolog service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    /**
     * Template service
     * @var \Magdev\Dossier\Service\TemplateService
     */
    protected $tpl = null;
    
    
    /**
     * Constructor
     * 
     * @param string $formatter
     */
    public function __construct(MonologService $logger, TemplateService $tpl, $formatter = '\\Leafo\\ScssPhp\\Formatter\\Compressed')
    {
        $this->logger = $logger;
        $this->tpl = $tpl;
        
        $this->lessc = new \Less_Parser(array(
            'compress' => !getenv('APP_DEBUG')
        ));
        
        $this->scssc = new Compiler();
        if (!getenv('APP_DEBUG')) {
            $this->scssc->setFormatter($formatter);
        }
    }
    
    
    /**
     * Get the LESS parser object
     * 
     * @return \Less_Parser
     */
    public function getLessc(): \Less_Parser
    {
        return $this->lessc;
    }
    
    
    /**
     * Get the SASS parser object
     *
     * @return \Leafo\ScssPhp\Compiler
     */
    public function getScssc(): Compiler
    {
        return $this->scssc;
    }
    
    
    /**
     * Parse a file and get the CSS
     * 
     * @param string $file
     * @return string
     */
    public function parse(string $file): string
    {
        if (file_exists($file)) {
            $this->logger->debug('Pre-Processing stylesheet file: '.$file);
            
            $type = explode('.', basename($file))[1];
            if ($type == self::TYPE_LESS) {
                return $this->lessc->parse(file_get_contents($file))->getCss();
            }
            
            if ($type == self::TYPE_SCSS) {
                return $this->scssc->compile(file_get_contents($file));
            }
        }
        return '';
    }
    
    
    /**
     * Parse theme stylesheets
     * 
     * @return string
     */
    public function parseThemeStyles(): string
    {
        $css = '';
        $lessFile = $this->tpl->getThemeFile('less/global.less');
        if (file_exists($lessFile)) {
            $css .= $this->parse($lessFile);
        }
        
        $scssFile = $this->tpl->getThemeFile('scss/global.scss');
        if (file_exists($scssFile)) {
            $css .= $this->parse($scssFile);
        }
        
        return $css;
    }
    
    
    /**
     * Parse user stylesheet 
     * 
     * @return string
     */
    public function parseUserstyles(): string
    {
        $css = '';
        if (file_exists(PROJECT_ROOT.'/'.self::TYPE_LESS.'/userstyles.'.self::TYPE_LESS)) {
            $this->logger->debug('Pre-Processing user-stylesheet file: '.PROJECT_ROOT.'/'.self::TYPE_LESS.'/userstyles.'.self::TYPE_LESS);
            $css .= $this->parse(PROJECT_ROOT.'/'.self::TYPE_LESS.'/userstyles.'.self::TYPE_LESS);
        }
        if (file_exists(PROJECT_ROOT.'/'.self::TYPE_SCSS.'/userstyles.'.self::TYPE_SCSS)) {
            $this->logger->debug('Pre-Processing user-stylesheet file: '.PROJECT_ROOT.'/'.self::TYPE_SCSS.'/userstyles.'.self::TYPE_SCSS);
            $css .= $this->parse(PROJECT_ROOT.'/'.self::TYPE_SCSS.'/userstyles.'.self::TYPE_SCSS);
        }
        return $css;
    }
    
    
    /**
     * Export LESS variables
     * 
     * @param string $file
     * @param string $targetDir
     * @param string $type 
     * @return \Magdev\Dossier\Service\StylesheetProcessorService
     */
    public function exportVariables(string $file, string $targetDir = PROJECT_ROOT, string $type = self::TYPE_LESS): StylesheetProcessorService
    {
        $code = $this->cutFile($file, self::DELIM_VARS);
        if ($code) {
            $target = $targetDir.'/'.$type.'/variables.'.$type;
            file_put_contents($target, $code);
        }
        return $this;
    }
    
    
    /**
     * Export LESS Mixins
     * 
     * @param string $file
     * @param string $targetDir
     * @param string $type 
     * @return \Magdev\Dossier\Service\StylesheetProcessorService
     */
    public function exportMixins(string $file, string $targetDir = PROJECT_ROOT, string $type = self::TYPE_LESS): StylesheetProcessorService
    {
        $code = $this->cutFile($file, self::DELIM_MIXINS);
        if ($code) {
            $target = $targetDir.'/'.$type.'/mixins.'.$type;
            file_put_contents($target, $code);
        }
        return $this;
    }
    
    
    /**
     * Write basic userstyles.less
     * 
     * @param string $targetDir
     * @param string $type 
     * @return \Magdev\Dossier\Service\StylesheetProcessorService
     */
    public function writeBasicUserstylesheet(string $targetDir = PROJECT_ROOT, string $type = self::TYPE_LESS): StylesheetProcessorService
    {
        if ($type == self::TYPE_LESS) {
            $code = <<<EOT
/**
 * LESS userstyles for magdev/dossier
 */
EOT;
            $path = $targetDir.'/less/userstyles.less';
        } else if ($type == self::TYPE_SCSS) {
            $code = <<<EOT
/**
 * SCSS userstyles for magdev/dossier
 */
EOT;
            $path = $targetDir.'/scss/userstyles.scss';
        }
        $code .= <<<EOT

@import "mixins";
@import "variables";

// Gobal styles

@media print {
    // Print styles
}

@media screen {
    // Screen styles
}
EOT;
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, $code);
        return $this;
    }
    
    
    /**
     * Cut parts off a stylesheet file
     * 
     * @param string $file
     * @param string $delimiter (MIXINS or VARS)
     * @return string
     */
    protected function cutFile(string $file, string $delimiter): string
    {
        if ($delimiter == self::DELIM_MIXINS || $delimiter == self::DELIM_VARS) {
            $start = '// '.$delimiter.':START';
            $end = '// '.$delimiter.':END';
            
            $fp = fopen($file, 'r');
            $lines = array();
            $started = false;
            
            while ($line = fgets($fp, 1024) !== false) {
                if ($line == $start) {
                    $started = true;
                }
                if ($started) {
                    if ($line == $end) {
                        fclose($fp);
                        return implode(PHP_EOL, $lines);
                    } else {
                        $lines[] = $line;
                    }
                }
            }
            fclose($fp);
        }
        return '';
    }
}