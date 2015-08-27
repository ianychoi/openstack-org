<?php

class SummitForm extends BootstrapForm
{

    public function __construct($controller, $name, $actions) {
        parent::__construct(
            $controller, 
            $name, 
            $this->getSummitFields(),
            $actions
        );

        $this->setTemplate($this->class);

    }


    protected function getSummitFields() {
        $fields = FieldList::create(
            TextField::create('Name')->setAttribute('autofocus','TRUE'),
            TextField::create('SummitBeginDate'),
            TextField::create('SummitEndDate'),
            DropdownField::create('EventTypes','',SummitEventType::get("SummitEventType")->map("ID", "Title"))->setAttribute('data-role','tagsinput')->setAttribute('multiple','multiple')
        );

        return $fields;
    }


}