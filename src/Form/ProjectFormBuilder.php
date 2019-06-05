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
class ProjectFormBuilder extends Base\BaseFormBuilder implements Base\FormBuilderInterface
{
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormInterface::getName()
     */
    public function getName()
    {
        return 'form.project';
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
        
        $form->addField(new TextField('name', $this->translator->trans('form.project.name')));
        $form->addField(new TextField('shortDescription', $this->translator->trans('form.project.short_description')));
        $form->addField(new TextField('stack', $this->translator->trans('form.project.stack')));
        $form->addField(new TextField('status', $this->translator->trans('form.project.status')));
        $form->addField(new BooleanField('active', $this->translator->trans('form.project.active')));
        $this->addFlatGroupField($form, 'urls', false, 'project');
        $form->addField(new TextField('text', $this->translator->trans('form.project.text'), false));
        return $form;
    }
}

