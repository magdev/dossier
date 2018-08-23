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

use PDFShift\PDFShift;
use PDFShift\Exceptions\PDFShiftException;
use Magdev\Dossier\Service\Exceptions\ServiceExcepton;
use Symfony\Component\Console\Exception\RuntimeException;
use Magdev\Dossier\Util\DataCollector;

/**
 * Service to create PFs with PDFShift API
 * @author magdev
 *
 */
class PdfService
{
    /**
     * Configuration service
     * @var \Magdev\Dossier\Service\ConfigService
     */
    protected $config = null;
    
    /**
     * Monolog service
     * @var \Magdev\Dossier\Service\MonologService
     */
    protected $logger = null;
    
    /**
     * Template service
     * @var \Magdev\Dossier\Service\TemplateService
     */
    protected $tpl = null;
    
    /**
     * PDFShift API handler
     * @var \PDFShift\PDFShift
     */
    protected $pdf = null;
    
    
    /**
     * Constructor
     * 
     * @param \Magdev\Dossier\Service\ConfigService $config
     * @param \Magdev\Dossier\Service\MonologService $logger
     * @param \Magdev\Dossier\Service\TemplateService $tpl
     */
    public function __construct(ConfigService $config, MonologService $logger, TemplateService $tpl)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->tpl = $tpl;
        
        if (!$this->config->get('pdfshift.apikey')) {
            throw new ServiceException('PDFShift API-Key not set!');
        }
        
        try {
            PDFShift::setApiKey($this->config->get('pdfshift.apikey'));
            $this->pdf = new PDFShift(array(
                'sandbox' => getenv('APP_DEBUG'),
                'use_print' => $this->config->get('pdfshift.stylesheet.use_print'),
                'format' => $this->config->get('pdfshift.page.format'),
                'margin' => $this->config->get('pdfshift.page.margin'),
            ));
            
            if ($userAgent = $this->config->get('pdfshift.http.user_agent')) {
                $this->pdf->addHTTPHeader('user-agent', $userAgent);
            }
        } catch (PDFShiftException $e) {
            throw new ServiceExcepton('Error initializing PDFShift client', -1, $e);
        }
    }
    
    
    /**
     * Get the internal PDFSift handler
     * @return \PDFShift\PDFShift
     */
    public function getPdfShift(): PDFShift
    {
        return $this->pdf;
    }
    
    
    /**
     * Create the PDF 
     * 
     * @param string $htmlFile
     * @return string
     */
    public function createPdf(string $htmlFile, bool $addHeader = false, bool $addFooter = false, bool $addSecurity = false): string
    {
        $html = file_get_contents($htmlFile);
        $outputDir = dirname($htmlFile);
        $outputFilename = basename($htmlFile, '.html').'.pdf';
        
        if ($addSecurity) {
            $securityConfig = $this->config->get('pdfshift.security');
            if (isset($securityConfig['userPassword']) && isset($securityConfig['ownerPassword'])) {
                $this->pdf->protect($securityConfig);
            }
        }
        
        if ($addHeader) {
            $this->pdf->setHeader($this->createHeader(new DataCollector()), $this->config->get('pdfshift.header.spacing'));
        }
        
        if ($addFooter) {
            $this->pdf->setFooter($this->createHeader(new DataCollector()), $this->config->get('pdfshift.footer.spacing'));
        }
        
        $this->pdf->convert($html);
        $this->pdf->save($outputDir.'/'.$outputFilename);
        
        if (!file_exists($outputDir.'/'.$outputFilename)) {
            throw new RuntimeException('Failed to create PDF file at '.$outputDir.'/'.$outputFilename);
        }
        return $outputDir.'/'.$outputFilename;
    }
    
    
    /**
     * Create the header HTML
     * 
     * @param \Magdev\Dossier\Util\DataCollector $vars
     * @return string
     */
    public function createHeader(DataCollector $data): string
    {
        return $this->tpl->renderDocument('parts/pdf/header.html.twig', $data);
    }
    
    
    /**
     * Create the footer HTML
     *
     * @param \Magdev\Dossier\Util\DataCollector $vars
     * @return string
     */
    public function createFooter(DataCollector $data): string
    {
        return $this->tpl->renderDocument('parts/pdf/footer.html.twig', $data);
    }
}

