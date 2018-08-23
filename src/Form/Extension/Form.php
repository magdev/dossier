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
 
namespace Magdev\Dossier\Form\Extension;

use Droath\ConsoleForm\Form as BaseForm;
use Droath\ConsoleForm\Field\Field;
use Droath\ConsoleForm\Exception\FormException;
use Droath\ConsoleForm\FieldDefinitionInterface;
use Droath\ConsoleForm\Field\FieldInterface;
use Magdev\Dossier\Form\Base\DecoratableLabelInterface;
use Magdev\Dossier\Form\Traits\DecoratableLabelTrait;

/**
 * Base Form class
 * 
 * @author magdev
 */
class Form extends BaseForm implements DecoratableLabelInterface
{
    use DecoratableLabelTrait;
    
    /**
     * Callback to post-process form results
     * @var array
     */
    protected $formResultsCallbacks = array();
    
    /**
     * Callback to pre-process data on load
     * @var array
     */
    protected $formLoadCallbacks = array();
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Form::addField()
     */
    public function addField(FieldDefinitionInterface $field)
    {
        if ($field instanceof DecoratableLabelInterface) {
            $field->setLabelLength($this->getLabelLength())
                ->setLabelPrefix($this->getLabelPrefix());
        }
        $this->fields[] = $field;
        
        return $this;
    }
    
    /**
     * Get a field by name
     * 
     * @param string $name
     * @throws \Droath\ConsoleForm\Exception\FormException
     * @return \Droath\ConsoleForm\FieldDefinitionInterface
     */
    public function getField(string $name): FieldDefinitionInterface
    {
        foreach ($this->fields as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }
        throw new FormException('Field '.$field.' not found');
    }
    
    
    /**
     * Check if a field exists
     * 
     * @param string $name
     * @return bool
     */
    public function hasField(string $name): bool
    {
        try {
            $this->getField($name);
            return true;
        } catch (FormException $e) {
            return false;
        }
    }
    
    
    /**
     * Set default values
     * 
     * @param array $defaults
     * @return \Magdev\Dossier\Form\Extension\Form
     */
    public function setDefaults(array $defaults): Form
    {
        if ($this->hasFormLoadCallbacks()) {
            $defaults = $this->onFormLoad($defaults);
        }
        foreach ($defaults as $field => $value) {
            if ($this->hasField($field)) {
                $field = $this->getField($field)->setDefault($value);
            }
        }
        
        return $this;
    }
    
    
    /**
     * Check if the is already processed
     * 
     * @return bool
     */
    public function isProcessed(): bool
    {
        return sizeof($this->results) > 0 ? true : false;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Form::process()
     */
    public function process()
    {
        if (!$this->isProcessed()) {
            $this->results = $this->processFields($this->fields, $this->results);
            
            if ($this->hasFormResultsCallbacks()) {
                $this->results = $this->onFormResults($this->results);
            }
        }
        return $this;
    }
    
    
    /**
     * Ceck if a data value is set
     * 
     * @param string $name
     * @throws \Droath\ConsoleForm\Exception\FormException
     * @return bool
     */
    public function hasData(string $name): bool
    {
        if (!$this->isProcessed()) {
            throw new FormException('Please process the form first');
        }
        
        return isset($this->results[$name]) && !empty($this->results[$name]) ? true : false;
    }
    
    
    /**
     * Get  a value from result and optional delete the field
     * 
     * @param string $name
     * @param bool $unset
     * @throws \Droath\ConsoleForm\Exception\FormException
     * @return string|bool|array
     */
    public function getData(string $name = null, bool $unset = false, $default = null)
    {
        if (!$this->isProcessed()) {
            throw new FormException('Please process the form first');
        }
        
        if (is_null($name)) {
            return $this->results;
        }
        
        $value = $default;
        if ($this->hasData($name)) {
            $value = $this->results[$name];
            if ($unset) {
                unset($this->results[$name]);
            }
        }
        return $value;
    }
    
    
    /**
     * Get a value and strip it from results
     * 
     * @param string $key
     * @param string|boolean|array|null $default
     * @return string|boolean|array
     */
    public function stripData(string $key, $default = null)
    {
        return $this->hasData($key) ? $this->getData($key, true) : $default;
    }
    
    
    /**
     * Check if the form has result callbacks
     * 
     * @return bool
     */
    public function hasFormResultsCallbacks(): bool
    {
        return sizeof($this->formResultsCallbacks) > 0;
    }
    
    
    /**
     * Add a callback to perform on results after processing
     * 
     * @param callable $function
     * @return \Magdev\Dossier\Form\Extension\Form
     */
    public function addFormResultsCallback(callable $callback): Form
    {
        $this->formResultsCallbacks[] = $callback;
        return $this;
    }
    
    
    /**
     * Form results callback handler
     * 
     * @param array $results
     * @return array
     */
    public function onFormResults(array $results): array
    {
        foreach ($this->formResultsCallbacks as $callback) {
            $results = call_user_func_array($callback, array($results, $this));
        }
        return $results;
    }
    
    
    
    /**
     * Check if the form has load callbacks
     *
     * @return bool
     */
    public function hasFormLoadCallbacks(): bool
    {
        return sizeof($this->formLoadCallbacks) > 0;
    }
    
    
    /**
     * Add a callback to perform on results after processing
     *
     * @param callable $function
     * @return \Magdev\Dossier\Form\Extension\Form
     */
    public function addFormLoadCallback(callable $callback): Form
    {
        $this->formLoadCallbacks[] = $callback;
        return $this;
    }
    
    
    /**
     * Form results callback handler
     *
     * @param array $results
     * @return array
     */
    public function onFormLoad(array $results): arry
    {
        foreach ($this->formLoadCallbacks as $callback) {
            $results = call_user_func_array($callback, array($results, $this));
        }
        return $results;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Form::fieldConditionMet()
     */
    protected function fieldConditionMet(FieldInterface $field, array $results)
    {
        $conditions = [];
        
        foreach ($field->getCondition() as $field_name => $condition) {
            $field_value = $this->getFieldValue($field_name, $results);
            
            if (is_callable($condition)) {
                $conditions[] = call_user_func_array($condition, array($field_value, $results, $this));
            } else {
                
                if (is_array($field_value) && key_exists($field_name, $field_value)) {
                    $field_value = $field_value[$field_name];
                }
                
                $value = $condition['value'];
                $operation = $condition['operation'];
                
                switch ($operation) {
                    case "!=":
                        $conditions[] = $field_value != $value;
                        break;
                        
                    case '=':
                    default:
                        $conditions[] = $field_value == $value;
                        break;
                }
            }
        }
        $conditions = array_unique($conditions);
        
        return sizeof($conditions) === 1 ? reset($conditions) : false;
    }
}

