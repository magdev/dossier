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

namespace Magdev\Dossier\Model;


use Magdev\Dossier\Model\Traits\PhotoTrait;
use Magdev\Dossier\Model\Base\BaseModel;
use Mni\FrontYAML\Document;
use Magdev\Dossier\Model\Person\Contact;
use Magdev\Dossier\Analyzer\Base\AnalyzableInterface;
use Magdev\Dossier\Model\Base\PhotoInterface;
use Magdev\Dossier\Model\Person\Reference;

/**
 * Model for the person object
 *
 * @author magdev
 */
final class Person extends BaseModel implements PhotoInterface, AnalyzableInterface
{
    use PhotoTrait;
    
    /**
     * First name
     * @var string
     */
    protected $firstname = '';

    /**
     * Last name
     * @var string
     */
    protected $lastname = '';

    /**
     * Birthdate
     * @var string
     */
    protected $birthdate = '';

    /**
     * Birthplace
     * @var string
     */
    protected $birthplace = '';

    /**
     * Tagline
     * @var string
     */
    protected $tagline = '';
    
    /**
     * Current residence
     * @var string
     */
    protected $residence = '';

    /**
     * Status
     * @var string
     */
    protected $status = '';

    /**
     * Nationality
     * @var string
     */
    protected $nationality = '';
    
    /**
     * Work license
     * @var string
     */
    protected $workLicense = '';

    /**
     * Languages
     * @var array
     */
    protected $languages = array();
    
    /**
     * Personal Links
     * @var array
     */
    protected $links = array();
    
    /**
     * Contact
     * @var \ArrayObject
     */
    protected $contacts = null;
    
    /**
     * References
     * @var \ArrayObject
     */
    protected $references = null;
    
    /**
     * Personal Interests
     * @var array
     */
    protected $interests = array();
    
    
    /**
     * Constructor
     * 
     * @param Mni\FrontYAML\Document $document
     */
    public function __construct(Document $document)
    {
        $this->contacts = new \ArrayObject();
        $this->references = new \ArrayObject();
        parent::__construct($document);
    }
    
    /**
     * Get the first name
     *
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }


    /**
     * Get the last name
     *
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }


    /**
     * Get the full name
     *
     * @param bool $reversed
     * @return string
     */
    public function getName(bool $reversed = false): string
    {
        if ($reversed) {
            return $this->lastname.', '.$this->firstname;
        }
        return $this->firstname.' '.$this->lastname;
    }


    /**
     * Get the persons tagline
     * 
     * @return string
     */
    public function getTagline(): string
    {
        return $this->tagline;
    }
    
    
    /**
     * Get the persons current residence
     *
     * @return string
     */
    public function getResidence(): string
    {
        return $this->residence;
    }


    /**
     * Get the birthday date
     * 
     * @return \DateTime
     */
    public function getBirthdate(): \DateTime
    {
        if ($this->birthdate) {
            return new \DateTime($this->birthdate);
        }
        return null;
    }
    
    
    /**
     * Get the birthplace
     * 
     * @return string
     */
    public function getBirthplace(): string
    {
        return $this->birthplace;
    }
    
    
    /**
     * Get the family status
     * 
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
    
    
    /**
     * Get the nationality
     * 
     * @return string
     */
    public function getNationality(): string
    {
        return $this->nationality;
    }
    
    
    /**
     * Get languages
     * 
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
    
    
    /**
     * Get personal links
     * 
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }
    
    
    /**
     * Get contact info
     * 
     * @return \ArrayObject
     */
    public function getContacts(): \ArrayObject
    {
        return $this->contacts;
    }
    
    
    /**
     * Get references
     *
     * @return \ArrayObject
     */
    public function getReferences(): \ArrayObject
    {
        return $this->references;
    }
    
    
    /**
     * Get personal interests
     *
     * @return array
     */
    public function getInterests(): array
    {
        return $this->interests;
    }
    
    
    /**
     * Get the work license
     * 
     * @return string
     */
    public function getWorkLicense(): string
    {
        return $this->workLicense;
    }
    
    
    /**
     * Get the email address
     * 
     * @return string
     */
    public function getEmail(): string
    {
        return $this->getContactType('email')->getAddress();
    }
    
    
    /**
     * Get the phone number
     * 
     * @return string
     */
    public function getPhone(): string
    {
        return $this->getContactType('phone')->getAddress();
    }
    
    
    /**
     * Get a contact with a specific type
     * 
     * @param string $type
     * @return \Magdev\Dossier\ModelContact
     */
    public function getContactType(string $type): Contact
    {
        foreach ($this->getContacts() as $contact) {
            /* @var $contact \Magdev\Dossier\Model\Person\Contact */
            
            if ($contact->getType() == $type) {
                return $contact->getAddress();
            }
        }
        return '';
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Model\Base\BaseModel::loadArray()
     */
    protected function loadArray(array $data): BaseModel
    {
        foreach ($data as $key => $value) {
            if ($key == 'photo') {
                $this->setPhoto($value);
            } else if ($key == 'contact') {
                foreach ($value as $type => $address) {
                    if ($type == 'accounts' && is_array($address)) {
                        foreach ($address as $a) {
                            $contact = new Contact($a['address'], $a['type'], $a['active']);
                            if ($contact->isActive()) {
                                $this->contacts->append($contact);
                            }
                        }
                    } else {
                        $this->contacts->append(new Contact($address, $type));
                    }
                }
            } else if ($key == 'references') {
                foreach ($value as $reference) {
                    $ref = new Reference($reference);
                    if ($ref->isActive()) {
                        $this->references->append($ref);
                    }
                }
            } else {
                $this->setProperty($key, $value);
            }
        }
        return $this;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Analyzer\Base\AnalyzableInterface::getAnalyzerData()
     */
    public function getAnalyzerData(): array
    {
        $props = get_object_vars($this);
        foreach ($this->ignoredProperties as $prop) {
            unset($props[$prop]);
        }
        $props['text'] = $this->getContent();
        return $props;
    }
}
