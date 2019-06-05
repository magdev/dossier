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

namespace Magdev\Dossier\Util;

use Magdev\Dossier\Util\Base\DataCollectorInterface;

/**
 * Simple data collector
 *
 * @author magdev
 */
class DataCollector implements DataCollectorInterface
{
    /**
     * Store the data
     * @var array
     */
    protected $data = array();


    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if (is_array($data)) {
            $this->addData($data);
        }
    }


    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Util\Base\DataCollectorInterface::addData()
     */
    public function addData(array $data): DataCollectorInterface
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }


    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Util\Base\DataCollectorInterface::setData()
     */
    public function setData(string $key, $value): DataCollectorInterface
    {
        $this->data[$key] = $value;
        return $this;
    }


    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Util\Base\DataCollectorInterface::getData()
     */
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Util\Base\DataCollectorInterface::hasData()
     */
    public function hasData(): bool
    {
        return sizeof($this->data) > 0 ? true : false;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Util\Base\DataCollectorInterface::merge()
     */
    public function merge(DataCollectorInterface $data): DataCollectorInterface
    {
        $this->addData($data->getData());
        return $this;
    }
}

