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
 
namespace Magdev\Dossier\Form\Field;

use Droath\ConsoleForm\Field\SelectField as BaseSelectField;
use Magdev\Dossier\Form\Base\DecoratableLabelInterface;
use Magdev\Dossier\Form\Traits\DecoratableLabelTrait;

/**
 * Extension for Field class to allow multiselects
 * 
 * @author magdev
 */
class SelectField extends BaseSelectField implements DecoratableLabelInterface
{
    use DecoratableLabelTrait;
    
    /**
     * Multiselect status
     * @var bool
     */
    protected $multiSelect = false;
    
    
    /**
     * Set field to multiselect
     * 
     * @param bool $multiSelect
     * @return \Magdev\Dossier\Form\Field\SelectField
     */
    public function setMultiSelect(bool $multiSelect = true): SelectField
    {
        $this->multiSelect = $multiSelect;
        return $this;
    }
    
    
    /**
     * Checkif field is multiselect
     * 
     * @return bool
     */
    public function isMultiSelect(): bool
    {
        return $this->multiSelect;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Field\SelectField::dataType()
     */
    public function dataType()
    {
        return $this->isMultiSelect() ? 'array' : 'string';
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Field\Field::formattedLabel()
     */
    protected function formattedLabel()
    {
        $label[] = '<fg=cyan;options=bold>'.$this->decorateLabel($this->label).'</>';
        $label[] = isset($this->default) ? '<fg=yellow;options=bold>['.$this->default.']</>' : '';
        $label[] = $this->isRequire() ? '<fg=red;options=bold>[*]</>: ' : ': ';
        
        return implode(' ', $label);
    }
}

