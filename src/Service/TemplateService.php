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

use Symfony\Component\Translation\Translator;
use Magdev\Dossier\Util\Base\DataCollectorInterface;
use Mni\FrontYAML\Parser;

/**
 * Template service 
 * 
 * @author magdev
 */
class TemplateService
{
    /**
     * Twig object
     * @var \Twig_Environment
     */
    protected $twig = null;
    
    /**
     * Markdown service
     * @var \Magdev\Dossier\Service\MarkdownService
     */
    protected $markdown = null;
    
    /**
     * Translator service
     * @var \Magdev\Dossier\Service\TranslatorService
     */
    protected $translator = null;
    
    /**
     * HTML minifer service
     * @var \Magdev\Dossier\Service\MinifierService
     */
    protected $minifier = null;
    
    /**
     * Configuration service
     * @var \Magdev\Dossier\Service\ConfigService
     */
    protected $config = null;
    
    /**
     * Name of the current theme
     * @var string
     */
    protected $theme = '';
    
    /**
     * Name of the document
     * @var string
     */
    protected $docname = 'document';
    
    /**
     * Monolog service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    
    /**
     * Constructor
     * 
     * @param \Magdev\Dossier\Service\MarkdownService $markdown
     * @param \Magdev\Dossier\Service\TranslatorService $translator
     * @param \Magdev\Dossier\Service\MinifierService $minifier
     * @param \Magdev\Dossier\Service\ConfigService $config
     * @param \Magdev\Dossier\Service\MonologService $logger
     */
    public function __construct(MarkdownService $markdown, TranslatorService $translator, MinifierService $minifier, ConfigService $config, MonologService $logger)
    {
        $this->markdown = $markdown;
        $this->translator = $translator;
        $this->minifier = $minifier;
        $this->config = $config;
        $this->logger = $logger;
    }
    
    
    /**
     * Get a file from a theme directory
     * 
     * @param string $file
     * @param string $theme
     * @return string
     */
    public function getThemeFile(string $file, string $theme = ''): string
    {
        $theme = !$theme ? $this->theme : $theme;
        
        if (getenv('DOSSIER_THEME_DIR')) {
            if (is_dir(getenv('DOSSIER_THEME_DIR').'/'.$theme) && file_exists(getenv('DOSSIER_THEME_DIR').'/'.$theme.'/'.$file)) {
                return getenv('DOSSIER_THEME_DIR').'/'.$theme.'/'.$file;
            }
        }
        if (is_dir(getenv('HOME').'/.dossier/tpl/'.$theme) && file_exists(getenv('HOME').'/.dossier/tpl/'.$theme.'/'.$file)) {
            return getenv('HOME').'/.dossier/tpl/'.$theme.'/'.$file;
        }
        return DOSSIER_ROOT.'/app/tpl/'.$theme.'/'.$file;
    }
    
    
    /**
     * Set the theme
     * 
     * @param string $theme
     * @return \Magdev\Dossier\Service\TemplateService
     */
    public function setTheme(string $theme): TemplateService
    {        
        $this->theme = $theme;
        
        $loaders = array();
        if (getenv('DOSSIER_THEME_DIR') && is_dir(getenv('DOSSIER_THEME_DIR').'/'.$theme)) {
            $loaders[] = new \Twig_Loader_Filesystem(getenv('DOSSIER_THEME_DIR').'/'.$theme);
        }
        if (is_dir(getenv('HOME').'/.dossier/tpl/'.$theme)) {
            $loaders[] = new \Twig_Loader_Filesystem(getenv('HOME').'/.dossier/tpl/'.$theme);
        }
        $loaders[] = new \Twig_Loader_Filesystem(DOSSIER_ROOT.'/app/tpl/'.$theme);
        
        $this->twig = new \Twig_Environment(new \Twig_Loader_Chain($loaders), array(
            'cache' => new \Twig_Cache_Filesystem(DOSSIER_CACHE, \Twig_Cache_Filesystem::FORCE_BYTECODE_INVALIDATION),
            'debug' => getenv('APP_DEBUG'),
        ));
        $this->addTwigExtensions($this->translator->getTranslator(), $this->config);
        return $this;
    }
    
    
    /**
     * Get the name of the current theme
     * 
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }
    
    
    /**
     * Set the document name
     * 
     * @param string $docname
     * @return \Magdev\Dossier\Service\TemplateService
     */
    public function setDocumentName(string $docname): TemplateService
    {
        $this->docname = $docname;
        return $this;
    }
    
    
    /**
     * Get the internal Twig object
     * 
     * @return \Twig_Environment
     */
    public function getTwig(): \Twig_Environment
    {
        return $this->twig;
    }
    
    
    /**
     * Render the page
     * 
     * @param string $template
     * @param \Magdev\Dossier\Util\Base\DataCollectorInterface $data
     * @param string $destDir
     * @return string
     */
    public function render(string $template, DataCollectorInterface $data, string $destDir): string
    {
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        $vars = $data->getData();
        $name = isset($vars['name']) ? $vars['name'] : $this->docname;
        $name .= isset($vars['theme']) ? '.'.$vars['theme'] : '';
        $path = $destDir.'/'.$name.'.'.$this->translator->getTranslator()->getLocale().'.html';
        
        $html = $this->twig->render($template, $vars);
        //$html = $this->minifier->minify($html);
        if (!file_put_contents($path, $html)) {
            throw new \RuntimeException('Error writing output file: '.$path);
        }
        return $path;
    }
    
    
    /**
     * Render a template and return the resulted document
     * 
     * @param string $template
     * @param \Magdev\Dossier\Util\Base\DataCollectorInterface $data
     * @return string
     */
    public function renderDocument(string $template, DataCollectorInterface $data): string
    {
        $html = $this->twig->render($template, $data->getData());
        $html = $this->minifier->minify($html);
        return $html;
    }
    
    
    /**
     * Add Twig extensions
     *
     * @param $translator \Symfony\Component\Translation\Translator
     * @param $config \Magdev\Dossier\Service\ConfigService
     * @return \Magdev\Dossier\Service\TemplateService
     */
    private function addTwigExtensions(Translator $translator, ConfigService $config): TemplateService
    {
        $this->twig->addFilter(new \Twig_Filter('trans', function (string $id, array $parameters = array(), string $domain=null, string $locale=null) use ($translator) {
            return $translator->trans($id, $parameters, $domain, $locale);
        }));
            
        $this->twig->addFilter(new \Twig_Filter('transChoice', function (string $id, int $number, array $parameters = array(), string $domain = null, string $locale = null) use ($translator) {
            return $translator->transChoice($id, $number, $parameters, $domain, $locale);
        }));
                
        $this->twig->addFilter(new \Twig_Filter('md', function (string $string) {
            return \ParsedownExtra::instance('twig')->parse($string);
        }, array('is_safe' => array('html'))));
        
        $this->twig->addFilter(new \Twig_Filter('inlinemd', function (string $string) {
            return \ParsedownExtra::instance('twig')->line($string);
        }, array('is_safe' => array('html'))));
            
        $this->twig->addFilter(new \Twig_Filter('unixToDateTime', function (int $unixTime) {
            return new \DateTime('@'.$unixTime);
        }));
        
        $this->twig->addFilter(new \Twig_Filter('filesize', function (int $filesize) {
            if ($filesize > (1024*1024)) {
                return round(($filesize/(1024*1024)), 2).' MiB';
            }
            if ($filesize > (1024)) {
                return round(($filesize/1024), 2).' KiB';
            }
            return $filesize.' B';
        }));
            
        $this->twig->addFilter(new \Twig_Filter('split', function (string $string, string $split = ',') {
            return explode($split, $string);
        }));
        
        $this->twig->addFilter(new \Twig_Filter('merge', function (array $strings, string $glue = ', ') {
            return implode($glue, $strings);
        }, array('is_safe' => array('html'))));
        
        $this->twig->addFilter(new \Twig_Filter('splitmerge', function (string $string, string $split = ',', string $glue = '<br/>') {
            $parts = explode($split, $string);
            return $glue ? implode($glue, $parts) : $parts;
        }, array('is_safe' => array('html'))));
            
        $this->twig->addFilter(new \Twig_Filter('debug', function ($var) {
            return getenv('APP_DEBUG') == true ? '<code>'.print_r($var, true).'</code>' : '';
        }, array('is_safe' => array('html'))));
                
        $this->twig->addFunction(new \Twig_Function('is_today', function (\DateTime $checkDate) {
            return (new \DateTime())->diff($checkDate)->days == 0;
        }));
        
        $this->twig->addFunction(new \Twig_Function('is_disabled', function (bool $value) {
            return $value == true;
        }));
            
        $this->twig->addFunction(new \Twig_Function('config', function (string $key) use ($config) {
            if ($config->has($key)) {
                $value = $config->get($key);
                if (is_scalar($value)) {
                    return $value;
                }
                return json_encode($value, JSON_NUMERIC_CHECK);
            }
            return '';
        }));
        
        $this->twig->addFunction(new \Twig_Function('parseFilename', function(string $filename) {
            $parts = explode('.', $filename);
            return array(
                'name' => ucfirst($parts[0]),
                'theme' => ucfirst($parts[1]),
                'locale' => strtoupper($parts[2]),
                'type' => strtolower($parts[3]),
            );
        }));
        
        return $this;
    }
}

