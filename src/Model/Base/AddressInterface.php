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


/**
 * Interface for objects with a postal address
 * 
 * @author magdev
 */
interface AddressInterface
{
    /**
     * Get address line 1
     * 
     * @return string
     */
    public function getAddress1(): string;
    
    
    /**
     * Get address line 2
     *
     * @return string
     */
    public function getAddress2(): string;
    
    
    /**
     * Get the name of the city
     *
     * @return string
     */
    public function getCity(): string;
    
    
    /**
     * Get the postcode
     *
     * @return string
     */
    public function getPostcode(): string;
    
    
    /**
     * Get the country code
     *
     * @return string
     */
    public function getCountry(): string;
    
    
    /**
     * Get the full location string
     *
     * @return string
     */
    public function getLocation(): string;
}

