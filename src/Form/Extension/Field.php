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

use Droath\ConsoleForm\Field\Field as BaseField;
use Magdev\Dossier\Form\Field\SelectField;
use Magdev\Dossier\Form\Base\DecoratableLabelInterface;
use Magdev\Dossier\Form\Traits\DecoratableLabelTrait;

/**
 * Field extension to use multiselect option on SelectField
 * 
 * @author magdev
 */
class Field extends BaseField implements DecoratableLabelInterface
{
    use DecoratableLabelTrait;
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Field\Field::setCondition()
     */
    public function setCondition($field_name, $value, $operation = '=')
    {
        if (is_callable($value)) {
            $this->condition[$field_name] = $value;
            return $this;
        }
        $this->condition[$field_name] = [
            'value' => $value,
            'operation' => $operation
        ];
        
        return $this;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Field\Field::asQuestion()
     */
    public function asQuestion()
    {
        $instance = $this->questionClassInstance()
            ->setMaxAttempts($this->maxAttempt);
        
        if ($this->hidden) {
            $instance->setHidden(true);
        }
        
        if ($this instanceof SelectField) {
            $instance->setMultiselect($this->isMultiSelect());
        }
        
        if ($this->isRequire()) {
            $this->setValidation(function ($answer) {
                if ($answer == '' && $this->dataType() !== 'boolean') {
                    throw new \Exception('Field is required.');
                }
            });
        }
        
        $instance->setValidator(function ($answer) {
            foreach ($this->validation as $callback) {
                if (!is_callable($callback)) {
                    continue;
                }
                $callback($answer);
            }
            return $answer;
        });
        
        if (isset($this->normalizer) && is_callable($this->normalizer)) {
            $instance->setNormalizer($this->normalizer);
        }
        return $instance;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Field\Field::formattedLabel()
     */
    protected function formattedLabel()
    {
        $label[] = '<fg=cyan;options=bold>'.$this->decorateLabel($this->label, (string) $this->default, '[*]').'</> ';
        $label[] = isset($this->default) ? "<fg=yellow;options=bold>[$this->default]</> " : '';
        $label[] = $this->isRequire() ? '<fg=red;options=bold>[*]</>: ' : ': ';
        
        return implode('', $label);
    }
}

