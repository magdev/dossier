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
 
namespace Magdev\Dossier\Form;

use Magdev\Dossier\Form\Extension\Form;
use Magdev\Dossier\Form\Field\SelectField;
use Magdev\Dossier\Form\Field\TextField;
use Magdev\Dossier\Form\Field\BooleanField;

/**
 * EntryFormBuilder
 * 
 * @author magdev
 */
class EntryFormBuilder extends Base\BaseFormBuilder implements Base\FormBuilderInterface
{
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormInterface::getName()
     */
    public function getName()
    {
        return 'form.cv';
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormInterface::buildForm()
     */
    public function buildForm()
    {
        $form = new Form();
        $form->setLabelPrefix($this->config->get('form.label.prefix'))
            ->setLabelLength($this->config->get('form.label.length'))
            ->addFormLoadCallback($this->getUnflattenCallback())
            ->addFormResultsCallback($this->getFlattenCallback());
        
        $form->addField(new TextField('start_date', $this->translator->trans('form.cv.start_date')));
        $form->addField(new TextField('end_date', $this->translator->trans('form.cv.end_date')));
        $form->addField(new TextField('position', $this->translator->trans('form.cv.position')));
        $form->addField(new TextField('company', $this->translator->trans('form.cv.company')));
        $form->addField((new SelectField('tag', $this->translator->trans('form.cv.tag')))
            ->setOptions($this->config->get('cv.tags'))
            ->setDefault($this->config->get('cv.default_tag'))
            ->setRequired(true));
        $form->addField(new TextField('industry', $this->translator->trans('form.cv.industry')));
        $form->addField(new TextField('qualification', $this->translator->trans('form.cv.qualification'), false));
        $this->addFlatGroupField($form, 'skills', false, 'cv')
            ->addFlatGroupField($form, 'achievements', false, 'cv')
            ->addFlatGroupField($form, 'toolbox', false, 'cv');
        $form->addField(new TextField('text', $this->translator->trans('form.cv.text'), false));
        $form->addField(new BooleanField('use_in_resume', $this->translator->trans('form.cv.use_in_resume')));
        return $form;
    }
}

