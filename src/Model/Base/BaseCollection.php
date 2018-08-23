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
 
namespace Magdev\Dossier\Model\Base;

abstract class BaseCollection  extends \ArrayObject
{
    const SORT_ASC  = 'asc';
    const SORT_DESC = 'desc';
    
    /**
     * Set the sort direction
     * @var string
     */
    protected $sortDirection = self::SORT_ASC;
    
    /**
     * The last sort direction, used for reset
     * @var string
     */
    protected $storedDirection = null;
    
    
    /**
     * Set the sort direction
     *
     * @param string $sortDirection
     * @param bool $temporary
     * @return \Magdev\Dossier\Model\BaseCollection
     */
    public function setSortDirection(string $sortDirection, bool $temporary = false): BaseCollection
    {
        if (!$temporary) {
            $this->storedDirection = $this->sortDirection;
        }
        $this->sortDirection = $sortDirection;
        return $this;
    }
    
    
    /**
     * Reset a temporary sort direction
     *
     * @return \Magdev\Dossier\Model\BaseCollection
     */
    public function resetSortDirection(): BaseCollection
    {
        if ($this->storedDirection) {
            $this->sortDirection = $this->storedDirection;
            $this->storedDirection = null;
        }
        return $this;
    }
}