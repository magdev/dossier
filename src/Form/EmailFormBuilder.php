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
use Magdev\Dossier\Form\Base\BaseFormBuilder;
use Magdev\Dossier\Form\Base\FormBuilderInterface;
use Magdev\Dossier\Form\Field\TextField;
use Magdev\Dossier\Form\Field\SelectField;
use Magdev\Dossier\Form\Field\BooleanField;

class EmailFormBuilder extends BaseFormBuilder implements FormBuilderInterface
{
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormInterface::getName()
     */
    public function getName()
    {
        return 'form.email';
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormInterface::buildForm()
     */
    public function buildForm()
    {
        $form = new Form();
        $form->setLabelPrefix($this->config->get('form.label.prefix'))
            ->setLabelLength($this->config->get('form.label.length'));
        
        $form->addField(new TextField('company', $this->translator->trans('form.email.company')));
        $form->addField((new SelectField('salutation', $this->translator->trans('form.email.salutation')))
            ->setOptions(array(
                $this->translator->trans('form.email.salutation.female'),
                $this->translator->trans('form.email.salutation.male'),
            ))
            ->setRequired(true));
        $form->addField(new TextField('firstname', $this->translator->trans('form.email.firstname')));
        $form->addField(new TextField('lastname', $this->translator->trans('form.email.lastname')));
        $form->addField(new TextField('email', $this->translator->trans('form.email.email')));
        $form->addField(new TextField('offer_title', $this->translator->trans('form.email.offer_title')));
        $form->addField(new TextField('offer_link', $this->translator->trans('form.email.offer_link'), false));
        
        $form->addField((new BooleanField('add_subject', $this->translator->trans('form.email.add_subject')))
            ->setSubform(function($subform, $value) {
                if ($value === true) {
                    $form->addField(new TextField('subject', $this->translator->trans('form.email.subject')));
                }
            }));
        
        return $form;
    }
}

