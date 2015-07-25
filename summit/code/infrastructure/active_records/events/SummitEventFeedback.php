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
class SummitEventFeedback extends DataObject implements ISummitEventFeedBack
{
    private static $db = array
    (
        'Rate'         => 'Int',
        'Note'         => 'HTMLText',
        'Approved'     => 'Boolean',
        'ApprovedDate' => 'SS_DateTime',
    );

    private static $has_many = array
    (
    );

    private static $defaults = array
    (
        'Approved' => false
    );

    private static $has_one = array
    (
        'Owner'      => 'Member',
        'ApprovedBy' => 'Member',
        'Event'      => 'SummitEvent',
    );

    private static $summary_fields = array
    (
    );

    private static $searchable_fields = array
    (
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return int
     */
    public function getRate()
    {
        return (int)$this->getField('Rate');
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->getField('Note');
    }

    /**
     * @return ICommunityMember
     */
    public function getOwner()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Owner')->getTarget();
    }

    /**
     * @return ISummitEvent
     */
    public function getEvent()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Event')->getTarget();
    }
}