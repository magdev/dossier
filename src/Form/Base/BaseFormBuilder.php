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
 
namespace Magdev\Dossier\Form\Base;

use Magdev\Dossier\Service\ConfigService;
use Magdev\Dossier\Service\TranslatorService;
use Magdev\Dossier\Form\Extension\Form;
use Magdev\Dossier\Form\PersonFormBuilder;
use Magdev\Dossier\Form\Field\TextField;
use Droath\ConsoleForm\FieldGroup;

abstract class BaseFormBuilder implements FormBuilderInterface 
{
    protected $flatGroupFields = array();

    /**
     * Configuration service
     * @var \Magdev\Dossier\Service\ConfigService
     */
    protected $config = null;
    
    /**
     * Translation service
     * @var \Magdev\Dossier\Service\TranslatorService
     */
    protected $translator = null;
    
    
    /**
     * Get the configuration service
     * 
     * @return \Magdev\Dossier\Service\ConfigService
     */
    public function getConfig(): ConfigService
    {
        return $this->config;
    }


    /**
     * Set the configuration service
     * 
     * @param \Magdev\Dossier\Service\ConfigService $config
     * @return \Magdev\Dossier\Form\Base\FormBuilderInterface
     */
    public function setConfig(ConfigService $config): FormBuilderInterface
    {
        $this->config = $config;
        return $this;
    }


    /**
     * Get the translator service
     * 
     * @return \Magdev\Dossier\Service\TranslatorService
     */
    public function getTranslator(): TranslatorService
    {
        return $this->translator;
    }


    /**
     * Set the translator service
     * 
     * @param \Magdev\Dossier\Service\TranslatorService $translator
     * @return \Magdev\Dossier\Form\Base\FormBuilderInterface
     */
    public function setTranslator(TranslatorService $translator): FormBuilderInterface
    {
        $this->translator = $translator;
        return $this;
    }

    
    /**
     * Get the flatten callback
     * 
     * @return callable
     */
    protected function getFlattenCallback(): callable
    {
        $flatGroupFields = $this->flatGroupFields;
        
        return function(array $results, Form $form) use ($flatGroupFields){
            foreach ($flatGroupFields as $field) {
                if (isset($results[$field]) && is_array($results[$field])) {
                    array_walk($results[$field], function(&$value, $key) {
                        if (is_array($value)) {
                            $value = (string) array_shift($value);
                        }
                    });
                }
            }
            return $results;
        };
    }
    
    
    /**
     * Get the unflatten callback
     *
     * @return callable
     */
    protected function getUnflattenCallback(): callable
    {
        $flatGroupFields = $this->flatGroupFields;
        
        return function(array $results, Form $form) use ($flatGroupFields){
            foreach ($flatGroupFields as $field) {
                if (isset($results[$field]) && is_array($results[$field])) {
                    array_walk($results[$field], function(&$value, $key) {
                        if (!is_array($value)) {
                            $value = array($value);
                        }
                    });
                }
            }
            return $results;
        };
    }
    
    
    /**
     * Add a flat group array for single line array
     *
     * @param \Magdev\Dossier\Form\Extension\Form $form
     * @param string $key
     * @param bool $required
     * @param string $langPrefix
     * @return \Magdev\Dossier\Form\Base\PersonFormBuilder
     */
    protected function addFlatGroupField(Form $form, string $key, bool $required = true, string $langPrefix = ''): FormBuilderInterface
    {
        $name = substr($key, 0, -1);
        $label = 'form.';
        if ($langPrefix) {
            $label .= $langPrefix.'.';
        }
        $label .= $key;
        
        $this->flatGroupFields[] = $key;
        
        $form->addField((new FieldGroup($key))
            ->addFields(array(
                (new TextField($name, $this->translator->trans($label), false)),
            ))->setLoopUntil(function($result) use ($name) {
                if (!isset($result[$name]) || empty($result[$name])) {
                    return false;
                }
                return true;
            }));
        return $this;
    }
}

