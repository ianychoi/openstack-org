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
final class SummitAttendee extends DataObject implements ISummitAttendee
{

    private static $db = array
    (
        // https://www.eventbrite.com/developer/v3/formats/order/#ebapi-std:format-order
        'ExternalOrderId'         => 'Int',
        'TicketBoughtDate'        => 'SS_Datetime',
        'SharedContactInfo'       => 'Boolean',
        'SummitHallCheckedIn'     => 'Boolean',
        'SummitHallCheckedInDate' => 'SS_Datetime',
    );

    private static $has_many = array
    (
    );

    private static $defaults = array
    (
    );

    private static $many_many = array
    (
        'Schedule' => 'SummitEvent'
    );

    static $many_many_extraFields = array(
        'Schedule' => array
        (
            'IsCheckedIn' => "Boolean",
        ),
    );

    private static $has_one = array
    (
        'Member' => 'Member',
        'Summit' => 'Summit',
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
     * @return ICommunityMember
     */
    public function getMember()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Member')->getTarget();
    }

    /**
     * @return ISummit
     */
    public function getSummit()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Summit')->getTarget();
    }

    /**
     * @return DateTime
     */
    public function getTicketBoughtDate()
    {
        return $this->getField('TicketBoughtDate');
    }

    /**
     * @return bool
     */
    public function allowSharedContactInfo()
    {
        return $this->getField('SharedContactInfo');
    }

    /**
     * @return bool
     */
    public function isSummitHallCheckedIn()
    {
        return $this->getField('SummitHallCheckedIn');
    }

    /**
     * @return ISummitEvent[]
     */
    public function getSchedule()
    {
       return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Schedule')->toArray();
    }

    /**
     * @param ISummitEvent $summit_event
     * @return void
     */
    public function addToSchedule(ISummitEvent $summit_event)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Schedule')->add
        (
            $summit_event,
            array('IsCheckedIn'=> false)
        );
    }

    /**
     * @return void
     */
    public function clearSchedule()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Schedule')->removeAll();
    }

    /**
     * @param $external_order_id
     * @param ISummitTicketOrderService $order_service
     * @return void
     */
    public function placeOrder($external_order_id, ISummitTicketOrderService $order_service)
    {
        $order_info = $order_service->getOrderInfo($external_order_id);
    }

    /**
     * @return void
     */
    public function registerSummitHallChecking()
    {
       if($this->SummitHallCheckedIn) return;

       $this->SummitHallCheckedIn      = true;
       $this->SummitHallCheckedInDate =  MySQLDatabase56::nowRfc2822();
    }

    public function registerCheckInOnEvent(ISummitEvent $summit_event)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Schedule')->remove($summit_event);
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Schedule')->add
        (
            $summit_event,
            array('IsCheckedIn'=> true)
        );
    }

    public function getCMSFields()
    {

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );
        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));
        $f->addFieldsToTab('Root.Main', new CheckboxField('SharedContactInfo', 'Allow Shared Contact Info'));
        //$f->addFieldsToTab('Root.Main', new ListboxField('MemberID', 'Member', Member::get()->map('ID', 'Email')));

        return $f;
    }
}