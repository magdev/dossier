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


use Droath\ConsoleForm\Field\BooleanField as BaseBooleanField;
use Magdev\Dossier\Form\Base\DecoratableLabelInterface;
use Magdev\Dossier\Form\Traits\DecoratableLabelTrait;

/**
 * Override BooleanField
 * 
 * @author magdev
 */
class BooleanField extends BaseBooleanField implements DecoratableLabelInterface
{
    use DecoratableLabelTrait;
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Field\BooleanField::formattedValue()
     */
    public function formattedValue($value)
    {
        return $value === true ? true : false;
    }
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\Field\BooleanField::formattedLabel()
     */
    protected function formattedLabel()
    {
        // Convert boolean default into a readable format.
        $default = $this->default ? 'yes' : 'no';
        
        $l = $this->decorateLabel($this->label);
        $label[] = "<fg=cyan;options=bold>'.$l.'</>";
        $label[] = isset($default) ? "<fg=yellow>[$default]</>" : '';
        
        return implode(' ', $label);
    }
}
