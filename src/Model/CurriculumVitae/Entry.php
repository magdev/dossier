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

namespace Magdev\Dossier\Model\CurriculumVitae;

use Magdev\Dossier\Model\AbstractModel;
use Magdev\Dossier\Model\Traits\PhotoTrait;
use Magdev\Dossier\Model\Traits\ToggableTrait;
use Mni\FrontYAML\Document;
use Magdev\Dossier\Model\Base\BaseModel;
use Magdev\Dossier\Analyzer\Base\AnalyzableInterface;
use Magdev\Dossier\Model\Base\PhotoInterface;


/**
 * Model for CV entries
 *
 * @author magdev
 */
final class Entry extends BaseModel implements PhotoInterface, AnalyzableInterface
{
    use PhotoTrait;
    use ToggableTrait;
    
    /**
     * Start date
     * @var \DateTime
     */
    protected $startDate = null;

    /**
     * End date
     * @var \DateTime
     */
    protected $endDate = null;

    /**
     * Tag - either education, experience or other
     * @var string
     */
    protected $tag = '';

    /**
     * Employer information
     * @var string
     */
    protected $company = '';

    /**
     * Obtained Qualification, i.e. graduations
     * @var string
     */
    protected $qualification = '';

    /**
     * Name of the position or role
     * @var string
     */
    protected $position = '';

    /**
     * Obtained skills
     * @var array
     */
    protected $skills = array();

    /**
     * Achievements - for the sake of glory!
     * @var array
     */
    protected $achievements = array();

    /**
     * Industry title
     * @var string
     */
    protected $industry = '';

    /**
     * Use this entry in resume
     * @var bool
     */
    protected $useInResume = false;

    /**
     * Certificate models
     * @var \ArrayObject
     */
    protected $certs = null;
    
    /**
     * Notes
     * @var string
     */
    protected $notes = '';
    
    /**
     * Toolbox
     * @var array
     */
    protected $toolbox = array();


    /**
     * Constrctor
     *
     * @param \Mni\FrontYAML\Document $document
     */
    public function __construct(Document $document)
    {
        $this->certs = new \ArrayObject();
        parent::__construct($document);
    }


    /**
     * Get the start date
     *
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        if ($this->startDate) {
            $date = new \DateTime($this->startDate);
            $date->setTime(0, 0);
            return $date;
        }
        return null;
    }


    /**
     * Get the end date
     *
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        if ($this->endDate) {
            $date = new \DateTime($this->endDate);
            $date->setTime(23, 59, 59);
            return $date;
        }
        $date = new \DateTime();
        $date->setTime(23, 59, 59);
        return $date;
    }


    /**
     * Get the tag for this entry
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * get the company name
     *
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }


    /**
     * Get the qualification obtained
     *
     * @return string
     */
    public function getQualification(): string
    {
        return $this->qualification;
    }


    /**
     * Get the position/job title
     *
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }


    /**
     * Get the achievements
     *
     * @return array
     */
    public function getAchievements(): array
    {
        return $this->achievements;
    }


    /**
     * Get the obtained skills
     *
     * @return array
     */
    public function getSkills(): array
    {
        return $this->skills;
    }


    /**
     * Get the industry
     *
     * @return string
     */
    public function getIndustry(): string
    {
        return $this->industry;
    }
    
    
    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }


    /**
     * Check if this entry should be included in the resume
     *
     * @return bool
     */
    public function useInResume(): bool
    {
        return $this->useInResume;
    }


    /**
     * Get the attachted certificates
     *
     * @return \ArrayObject(\Magdev\Dossier\Model\CurriculumVitae\Entry\Certificate[])
     */
    public function getCertificates(): \ArrayObject
    {
        return $this->certs;
    }
    
    
    /**
     * Check if entry has certificates
     * 
     * @return bool
     */
    public function hasCertificates(): bool
    {
        return $this->certs->count() > 0;
    }
    
    
    /**
     * Get the toolbox
     *
     * @return \ArrayObject
     */
    public function getToolbox(): \ArrayObject
    {
        return new \ArrayObject($this->toolbox);
    }
    
    
    /**
     * Get the length of this entry in seconds
     * 
     * @return int
     */
    public function getExperienceLength(): int
    {
        return $this->getEndDate()->format('U') - $this->getStartDate()->format('U');
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
    

    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Model\Base\BaseModel::loadArray()
     */
    protected function loadArray(array $data): BaseModel
    {
        foreach ($data as $key => $value) {
            if ($key == 'certs') {
                foreach ($value as $cert) {
                    $c = new Entry\Certificate(PROJECT_ROOT.'/certs/'.$cert['path'], $cert['type']);
                    $this->certs->append($c);
                }
            } else if ($key == 'photo') {
                $this->setPhoto($value);
            } else {
                $prop = $this->convertCase($key);
                if (property_exists($this, $prop)) {
                    $this->{$prop} = $value;
                } else {
                    $this->addAdditionalData($prop, $value);
                }
            }
        }
        return $this;
    }
}
