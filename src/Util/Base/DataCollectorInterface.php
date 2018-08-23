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

namespace Magdev\Dossier\Util\Base;

/**
 * Interface for template data collectors
 *
 * @author magdev
 */
interface DataCollectorInterface
{
    /**
     * Add multiple data values at once
     *
     * @param array $data
     * @return \Magdev\Dossier\Util\Base\DataCollectorInterface
     */
    public function addData(array $data): DataCollectorInterface;


    /**
     * Set a single data value
     *
     * @param string $varname
     * @param unknown $value
     * @return \Magdev\Dossier\Util\Base\DataCollectorInterface
     */
    public function setData(string $varname, $value): DataCollectorInterface;


    /**
     * Get all data values as array
     *
     * @return array
     */
    public function getData(): array;


    /**
     * Get all data values as array
     *
     * @return bool
     */
    public function hasData(): bool;
    
    
    /**
     * Merge another data collector
     * 
     * @param \Magdev\Dossier\Util\Base\DataCollectorInterface $data
     * @return \Magdev\Dossier\Util\Base\DataCollectorInterface
     */
    public function merge(DataCollectorInterface $data): DataCollectorInterface;
}

