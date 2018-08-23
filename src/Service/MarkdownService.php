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
 
namespace Magdev\Dossier\Service;

use Mni\FrontYAML;
use Mni\FrontYAML\Bridge\Parsedown\ParsedownParser;
use Symfony\Component\Yaml\Yaml;
use Magdev\Dossier\Model\Base\ModelInterface;
use Magdev\Dossier\Model\Base\ModelExportableInterface;
use Magdev\Dossier\Model\CurriculumVitae;
use Magdev\Dossier\Model\Person;
use Magdev\Dossier\Model\Intro;
use Magdev\Dossier\Model\Project;
use Magdev\Dossier\Service\Exceptions\ServiceExcepton;
use Magdev\Dossier\Util\Base\DataCollectorInterface;
use Magdev\Dossier\Util\DataCollector;


/**
 * Markdown service
 * 
 * @author magdev
 */
class MarkdownService
{
    /**
     * Parser object
     * @var \Mni\FrontYAML\Parser
     */
    protected $parser = null;
    
    /**
     * Monolog service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    /**
     * Formatter service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $formatter = null;
    
    
    /**
     * Constructor
     */
    public function __construct(MonologService $logger, FormatterService $formatter) 
    {
        $this->parser = new FrontYAML\Parser(null, new ParsedownParser(new \ParsedownExtra()));
        $this->logger = $logger;
        $this->formatter = $formatter;
    }
    
    
    /**
     * Get the Markdown parser object
     * 
     * @return \Mni\FrontYAML\Parser
     */
    public function getParser(): FrontYAML\Parser
    {
        return $this->parser;
    }
    
    
    /**
     * Get a dataCollector with models of markdown files
     * 
     * @param string $sort
     * @return \Magdev\Dossier\Util\Base\DataCollectorInterface
     */
    public function getFileSet(string $sort = CurriculumVitae::SORT_DESC): DataCollectorInterface
    {
        $person = new Person($this->getDocument(PROJECT_ROOT.'/person.md'));
        $intro = new Intro($this->getDocument(PROJECT_ROOT.'/intro.md'));
        
        $cv = new CurriculumVitae($this->formatter);
        $files = new \FilesystemIterator(PROJECT_ROOT.'/cv');
        foreach ($files as $file) {
            /* @var $file \SplFileInfo */
            $document = $this->getDocument($file->getPathname());
            $cv->append(new CurriculumVitae\Entry($document));
        }
        $cv->setSortDirection($sort)->sort();
        
        $projects = new \ArrayObject();
        $files = new \FilesystemIterator(PROJECT_ROOT.'/projects');
        foreach ($files as $file) {
            /* @var $file \SplFileInfo */
            $document = $this->getDocument($file->getPathname());
            $projects->append(new Project($document));
        }
        
        
        return new DataCollector(array(
            'intro' => $intro,
            'person' => $person,
            'cv' => $cv,
            'projects' => $projects,
        ));
    }
    
    
    /**
     * Parse a markdown file
     *
     * @param $srcfile string
     * @return \Mni\FrontYAML\Document
     */
    public function getDocument(string $srcfile, bool $parseMarkdown = true): FrontYAML\Document
    {
        $content = '';
        if (file_exists($srcfile)) {
            $content = file_get_contents($srcfile);
            $this->logger->debug(mb_strlen($content).' bytes loaded from file '.$srcfile);
        }
        return $this->getParser()->parse($content, $parseMarkdown);
    }
    
    
    /**
     * Save Markdown data
     * 
     * @param string $path
     * @param array $data
     * @param string $text
     * @param bool $overwrite
     * @return \Magdev\Dossier\Service\MarkdownService
     */
    public function save(string $path, array $data, string $text, bool $overwrite = true): MarkdownService
    {
        $content = '';
        
        if (sizeof($data)) {
            $content .= '---'.PHP_EOL;
            $content .= Yaml::dump($data, 1, 2);
            $content .= '---'.PHP_EOL;
        }
        
        if ($text) {
            $content .= $text.PHP_EOL;
        }
        
        return $this->saveFile($path, $content, $overwrite);
    }
    
    
    /**
     * Save the contents to a file and creates backup copy
     * 
     * @param string $path
     * @param string $content
     * @param bool $overwrite
     * @return \Magdev\Dossier\Service\MarkdownService
     */
    protected function saveFile(string $path, string $content, bool $overwrite = true): MarkdownService
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        if ($content) {
            if ($overwrite) {
                if (file_exists($path.'~')) {
                    @unlink($path.'~');
                }
                @copy($path, $path.'~');
                
                if (file_put_contents($path, $content) === false) {
                    throw new ServiceExcepton('Error while writing '.$path);
                }
                $this->logger->debug(mb_strlen($content).' bytes written to file '.$path);
                return $this;
            } 
            if (!file_exists($path)) {
                if (file_put_contents($path, $content) === false) {
                    throw new ServiceExcepton('Error while writing '.$path);
                }
                $this->logger->debug(mb_strlen($content).' bytes written to file '.$path);
            }
        }
        return $this;
    }
}