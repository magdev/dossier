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
 
namespace Magdev\Dossier\Model\Person;

use Magdev\Dossier\Model\Base\BaseModel;

/**
 * Model for job references
 * 
 * @author magdev
 */
class Reference extends BaseModel
{
    /**
     * Reference description
     * @var string
     */
    protected $decription = ''; 
    
    /**
     * Name of a possible contact person
     * @var string
     */
    protected $contactName = '';
    
    /**
     * Email address of a possible contact person
     * @var string
     */
    protected $contactEmail = '';
    
    /**
     * Phone number of a possible contact person
     * @var string
     */
    protected $contactPhone = ''; 
    
    
    /**
     * Refrence is published and can be viewed online
     * @var bool
     */
    protected $public = false;
    
    /**
     * Link to a published reference
     * @var string
     */
    protected $publicLink = ''; 
    
    
    /**
     * Example work as file
     * @var \SplFileObject
     */
    protected $file = null;
    
    /**
     * Link to an example work
     * @var string
     */
    protected $fileLink = null;
    
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->loadArray($data);
    }
    
    
    /**
     * Get the description of the reference
     * 
     * @return string
     */
    public function getDecription(): string
    {
        return $this->decription;
    }


    /**
     * Get the name of a possible contact person
     * 
     * @return string
     */
    public function getContactName(): string
    {
        return $this->contactName;
    }
    
    
    /**
     * Get the email address of a possible contact person
     *
     * @return string
     */
    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }
    
    
    /**
     * Get the phone number of a possible contact person
     *
     * @return string
     */
    public function getContactPhone(): string
    {
        return $this->contactPhone;
    }


    /**
     * Check if the reference is published and can be viewed online
     * 
     * @return boolean
     */
    public function isPublic(): bool
    {
        return $this->public;
    }


    /**
     * Lik to a published example work
     * 
     * @return unknown
     */
    public function getPublicLink(): string
    {
        return $this->publicLink;
    }


    /**
     * Example work file
     * 
     * @return \SplFileObject
     */
    public function getFile(): \SplFileObject
    {
        return $this->file;
    }


    /**
     * Get the link to a file with example work
     * 
     * @return string
     */
    public function getFileLink(): string
    {
        return $this->fileLink;
    }

}

