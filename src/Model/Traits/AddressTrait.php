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
 
namespace Magdev\Dossier\Model\Traits;

trait AddressTrait
{
    /**
     * Address line 1
     * @var string
     */
    protected $address1 = '';
    
    /**
     * Address line 2
     * @var string
     */
    protected $address2 = '';
    
    /**
     * Name of the city
     * @var string
     */
    protected $city = '';
    
    /**
     * Postcode
     * @var string
     */
    protected $postcode = '';
    
    /**
     * Conutry code
     * @var string
     */
    protected $country = '';
    
    
    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Model\Base\AddressInterface::getAddress1()
     */
    public function getAddress1(): string
    {
        return $this->address1;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Model\Base\AddressInterface::getAddress2()
     */
    public function getAddress2(): string
    {
        return $this->address2;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Model\Base\AddressInterface::getCity()
     */
    public function getCity(): string
    {
        return $this->city;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Model\Base\AddressInterface::getPostcode()
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Model\Base\AddressInterface::getCountry()
     */
    public function getCountry(): string
    {
        return $this->country;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Model\Base\AddressInterface::getLocation()
     */
    public function getLocation(): string
    {
        $location = '';
        if ($this->postcode) {
            $location .= $this->postcode.' ';
        }
        if ($this->city) {
            $location .= $this->city;
        }
        if ($this->country) {
            $location .= ' ('.$this->country.')';
        }
        return $location;
    }
}
