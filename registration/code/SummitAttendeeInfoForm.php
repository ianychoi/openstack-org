<?php

/**
 * Copyright 2015 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
final class SummitAttendeeInfoForm extends SafeXSSForm
{
    function __construct($controller, $name)
    {
        $fields = new FieldList
        (
            array
            (
                new TextField('ExternalOrderId', 'Eventbrite Order #'),
                new CheckboxField('SharedContactInfo', 'Allow to share contact info?')
            )
        );
        // Create action
        $actions = new FieldList
        (
            new FormAction('saveSummitAttendeeInfo', 'Save')
        );

        $validator = null;

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    public function loadDataFrom($data, $mergeStrategy = 0, $fieldList = null) {
        if($data && $data->ID > 0)
        {
            $this->fields->insertAfter($t1 = new TextField('TicketBoughtDate', 'Ticket Bought Date'),'ExternalOrderId');
            $t1->setReadonly(true);
        }
        parent::loadDataFrom($data, $mergeStrategy, $fieldList);
    }
}