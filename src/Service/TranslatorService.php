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
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Translator Service
 * 
 * @author magdev
 */
class TranslatorService
{
    /**
     * Translator Object
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator = null;
    
    /**
     * Fallback locales
     * @var array
     */
    protected $fallbackLocales = array();
    
    /**
     * Monolog service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    
    /**
     * Constructor
     * 
     * @param string $locale
     */
    public function __construct(ConfigService $config, MonologService $logger)
    {
        $this->logger = $logger;
        $this->fallbackLocales = $config->get('translator.fallback_locales');
        $this->loadLocale($config->get('translator.locale'));
    }
    
    
    /**
     * Set the locale
     * 
     * @param string $locale
     * @return \Magdev\Dossier\Service\TranslatorService
     */
    public function setLocale(string $locale): TranslatorService
    {
        return $this->loadLocale($locale);
    }
    
    
    /**
     * Get the internal Translator object
     * 
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }
    
    
    /**
     * Translate a string 
     * 
     * @param string $id
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @see \Symfony\Component\Translation\Translator::trans()
     * @return string
     */
    public function trans(string $id, array $parameters = array(), string $domain = null, string $locale = null): string
    {
        return $this->getTranslator()->trans($id, $parameters, $domain, $locale);
    }
    
    
    /**
     * Translate plural strings 
     * 
     * @param string $id
     * @param int $number
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @see \Symfony\Component\Translation\Translator::transChoice()
     * @return string
     */
    public function transChoice(string $id, int $number, array $parameters = array(), string $domain = null, string $locale = null): string
    {
        return $this->getTranslator()->transChoice($id, $number, $parameters, $domain, $locale);
    }
    
    
    /**
     * Load a locale 
     * 
     * @param string $locale
     * @return \Magdev\Dossier\Service\TranslatorService
     */
    protected function loadLocale(string $locale): TranslatorService
    {
        $this->translator = new Translator($locale);
        $this->translator->setFallbackLocales($this->fallbackLocales);
        $this->translator->addLoader('yaml', new YamlFileLoader());
        
        $this->translator->addResource('yaml', DOSSIER_ROOT.'/app/locale/messages.'.$locale.'.yaml', $locale);
        
        if (file_exists(getenv('HOME').'/.dossier/locale/messages.'.$locale.'.yaml')) {
            $this->translator->addResource('yaml', getenv('HOME').'/.dossier/locale/messages.'.$locale.'.yaml', $locale);
        }
        
        if (file_exists(PROJECT_ROOT.'/.conf/locale/messages.'.$locale.'.yaml')) {
            $this->translator->addResource('yaml', PROJECT_ROOT.'/.conf/locale/messages.'.$locale.'.yaml', $locale);
        }
        return $this;
    }
}