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

use Magdev\Dossier\Model\CurriculumVitae\Entry;
use Magdev\Dossier\Service\FormatterService;
use Magdev\Dossier\Model\Base\BaseCollection;

/**
 * Collection of CV-Entries
 *
 * @author magdev
 */
class CurriculumVitae extends BaseCollection
{
    /**
     * Formatter Service
     * @var \Magdev\Dossier\Service\FormatterService
     */
    protected $formatter = null;
    
    /**
     * Constructor
     * 
     * @param \Magdev\Dossier\Service\FormatterService $formatter
     */
    public function __construct(FormatterService $formatter)
    {
        $this->formatter = $formatter;
    }
    
    /**
     * Filter entries by tag
     *
     * @param string $tag
     * @return \Magdev\Dossier\Model\CurriculumVitae
     */
    public function filterByTag(string $tag): CurriculumVitae
    {
        $result = new self($this->formatter);
        foreach ($this as $entry) {
            /* @var $entry \Magdev\Dossier\Model\CurriculumVitae\Entry */
            if ($entry->getTag() == $tag) {
                $result->append($entry);
            }
        }
        $result->setSortDirection($this->sortDirection)->sort();
        return $result;
    }


    /**
     * Filter entries by industry
     *
     * @param string $industry
     * @return \Magdev\Dossier\Model\CurriculumVitae
     */
    public function filterByIndustry(string $industry): CurriculumVitae
    {
        $result = new self($this->formatter);
        foreach ($this as $entry) {
            /* @var $entry \Magdev\Dossier\Model\CurriculumVitae\Entry */
            if ($entry->getIndustry() == $industry) {
                $result->append($entry);
            }
        }
        $result->setSortDirection($this->sortDirection)->sort();
        return $result;
    }
    
    
    /**
     * Filter entries by certificates
     *
     * @return \Magdev\Dossier\Model\CurriculumVitae
     */
    public function filterByCertificates(): CurriculumVitae
    {
        $result = new self($this->formatter);
        foreach ($this as $entry) {
            /* @var $entry \Magdev\Dossier\Model\CurriculumVitae\Entry */
            if ($entry->hasCertificates()) {
                $result->append($entry);
            }
        }
        $result->setSortDirection($this->sortDirection)->sort();
        return $result;
    }
    
    
    /**
     * Get entries with qualification
     *
     * @return \Magdev\Dossier\Model\CurriculumVitae
     */
    public function getQualifications(): CurriculumVitae
    {
        $result = new self($this->formatter);
        foreach ($this as $entry) {
            /* @var $entry \Magdev\Dossier\Model\CurriculumVitae\Entry */
            if ($entry->getQualification()) {
                $result->append($entry);
            }
        }
        $result->setSortDirection($this->sortDirection)->sort();
        return $result;
    }


    /**
     * Filter entries used for resume
     *
     * @param bool $useInResume
     * @return \Magdev\Dossier\Model\CurriculumVitae
     */
    public function filterByUseInResume(bool $useInResume = true): CurriculumVitae
    {
        $result = new self($this->formatter);
        foreach ($this as $entry) {
            /* @var $entry \Magdev\Dossier\Model\CurriculumVitae\Entry */
            if ($entry->useInResume()) {
                $result->append($entry);
            }
        }
        $result->setSortDirection($this->sortDirection)->sort();
        return $result;
    }
    
    
    /**
     * Filter entries by toolbox contents
     * 
     * @return \Magdev\Dossier\Model\CurriculumVitae
     */
    public function filterByToolbox(): CurriculumVitae
    {
        $result = new self($this->formatter);
        foreach ($this as $entry) {
            /* @var $entry \Magdev\Dossier\Model\CurriculumVitae\Entry */
            if ($entry->getToolbox()->count() > 0) {
                $result->append($entry);
            }
        }
        $result->setSortDirection($this->sortDirection)->sort();
        return $result;
    }


    /**
     * Get the length of experience sorted by industry
     *
     * @return \ArrayObject
     */
    public function getExperienceYears(): \ArrayObject
    {
        $result = new \ArrayObject();
        $entries = $this->filterByTag('experience');
        foreach ($entries as $entry) {
            /* @var $entry \Magdev\Dossier\Model\CurriculumVitae\Entry */
            if ($industry = $entry->getIndustry()) {
              if (!array_key_exists($industry, $result)) {
                  $result[$industry] = 0;
              }
              $result[$industry] += $entry->getExperienceLength();
            }
        }
        $result->uasort(function(int $a, int $b) {
            return $a > $b ? -1 : 1;
        });
        
        $formatter = $this->formatter;
        array_walk($result, function(int &$seconds, string $key) use ($formatter) {
            $seconds = $formatter->formatExperience($seconds);
        });
        return $result;
    }
    
    
    /**
     * Sort by start date
     *
     * @return \Magdev\Dossier\Model\CurriculumVitae
     */
    public function sort(): CurriculumVitae
    {
        $this->uasort(array($this, 'sortByStartDate'));
        return $this;
    }
    
    
    
    /**
     * Sort function to sort by start date
     *
     * @param \Magdev\Dossier\Model\CurriculumVitae\Entry $a
     * @param \Magdev\Dossier\Model\CurriculumVitae\Entry $b
     * @return int
     */
    protected function sortByStartDate(Entry $a, Entry $b): int
    {
        $first = $a->getStartDate()->format('U');
        $second = $b->getStartDate()->format('U');
        
        if ($first == $second) {
            return 0;
        }
        if ($this->sortDirection == self::SORT_DESC) {
            return $first > $second ? -1 : 1;
        }
        return $first < $second ? -1 : 1;
    }
}
