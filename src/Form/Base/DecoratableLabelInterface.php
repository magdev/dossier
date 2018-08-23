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

/**
 * Interface for elements with decorable label
 * 
 * @author magdev
 */
interface DecoratableLabelInterface
{
    /**
     * Set a label prefix
     *
     * @param string $prefix
     * @return \Magdev\Dossier\Form\Base\DecoratableLabelInterface
     */
    public function setLabelPrefix(string $prefix): DecoratableLabelInterface;
    
    
    /**
     * Set the fixed length of the label
     * 
     * @param int $length
     * @return \Magdev\Dossier\Form\Base\DecoratableLabelInterface
     */
    public function setLabelLength(int $length): DecoratableLabelInterface;
    
    
    /**
     * Get the label prefix
     * 
     * @return string
     */
    public function getLabelPrefix(): string;
    
    
    /**
     * Get the label length
     * 
     * @return int
     */
    public function getLabelLength(): int;
    
    
    /**
     * Get the decorated label
     *
     * @param string $label
     * @return string
     */
    public function decorateLabel(string $label): string;
}

