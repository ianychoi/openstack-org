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
class SummitEvent extends DataObject implements ISummitEvent
{


    protected $already_converted_date = false;

    private static $db = array
    (
        'Title'         => 'Text',
        'Description'   => 'HTMLText',
        'StartDate'     => 'SS_Datetime',
        'EndDate'       => 'SS_Datetime',
        'Published'     => 'Boolean',
        'PublishedDate' => 'SS_Datetime',
        'AllowFeedBack' => 'Boolean',
    );

    private static $has_many = array
    (
        'Feedback' => 'SummitEventFeedback',
    );

    private static $defaults = array
    (
    );

    private static $many_many = array
    (
        'AllowedSummitTypes' => 'SummitType',
        'Sponsors'           => 'Company',
    );

    private static $belongs_many_many = array
    (
        'Attendees'   => 'SummitAttendee',
    );

    private static $has_one = array
    (
        'Location' => 'SummitAbstractLocation',
        'Summit'   => 'Summit',
        'Type'     => 'SummitEventType',
    );

    private static $summary_fields = array
    (
        'Title' => 'Event Title',
        'StartDateNice' => 'Event Start Date',
        'EndDateNice' => 'Event End Date',
        'LocationNameNice' => 'Location',
        'TypeName' => 'Event Type',
    );

    private static $searchable_fields = array
    (
        'Title',
        'StartDate',
        'EndDate',
        'Location.Name',
        'Type.Type',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getLink() {
        return $this->Summit()->Link.'schedule/event/'.$this->getIdentifier().'/'.$this->getTitleForUrl();
    }

    public function getTitleForUrl() {
        $lcase_title = strtolower(trim($this->Title));
        $title_for_url = str_replace(' ','-',$lcase_title);
        return $title_for_url;
    }

    public function getLocationName()
    {
        if($this->Location()->ID > 0)
        {
            return $this->Location()->Name;
        }
        return 'TBD';
    }

    public function getLocationNameNice()
    {
        if($this->Location()->ID > 0)
        {
            return $this->Location()->Name;
        }
        return 'TBD';
    }
    /**
     * @return DateTime
     */
    public function getStartDateOnTimeZone()
    {
        $start_date =  $this->getField('StartDate');
        if(empty($start_date)) return null;

        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);

        $time_zone_id   = $summit->TimeZone;
        $time_zone_list = timezone_identifiers_list();

        if(isset($time_zone_list[$time_zone_id]))
        {
            $utc_timezone      = new DateTimeZone("UTC");
            $time_zone_name = $time_zone_list[$time_zone_id];
            $time_zone   = new \DateTimeZone($time_zone_name);
            $start_date    = new \DateTime($start_date, $utc_timezone);
            $start_date->setTimezone($time_zone);
            return $start_date->format("Y-m-d H:i:s");
        }

        return null;
    }

    public function getStartDateNice()
    {
        $start_date =  $this->getStartDateOnTimeZone();
        if(empty($start_date)) return 'TBD';
        return $start_date;
    }

    /**
     * @return DateTime
     */
    public function getEndDateOnTimeZone()
    {
        $end_date =  $this->getField('EndDate');
        if(empty($end_date)) return null;


        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);

        $time_zone_id   = $summit->TimeZone;
        $time_zone_list = timezone_identifiers_list();

        if(isset($time_zone_list[$time_zone_id]))
        {
            $utc_timezone      = new DateTimeZone("UTC");
            $time_zone_name = $time_zone_list[$time_zone_id];
            $time_zone   = new \DateTimeZone($time_zone_name);
            $end_date    = new \DateTime($end_date, $utc_timezone);

            $end_date->setTimezone($time_zone);
            return $end_date->format("Y-m-d H:i:s");
        }

        return null;
    }

    public function getEndDateNice()
    {
        $end_date  = $this->getEndDateOnTimeZone();
        if(empty($end_date)) return 'TBD';
        return $end_date;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getField('Description');
    }

    /**
     * @return ISummitLocation
     */
    public function getLocation()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Location')->getTarget();
    }

    /**
     * @return ICompany[]
     */
    public function getSponsors()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Sponsors')->toArray();
    }

    /**
     * @return ISummitEventType
     */
    public function getType()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Type')->getTarget();
    }

    public function getTypeName()
    {
        return $this->getType()->Type;
    }

    /**
     * @return ISummitType[]
     */
    public function getAllowedSummitTypes()
    {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'AllowedSummitTypes')->toArray();
    }

    /**
     * @return ISummit
     */
    public function getSummit()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Summit')->getTarget();
    }

    /**
     * @return ISummitEventFeedBack[]
     */
    public function getFeedback()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Feedback')->toArray();
    }

    /**
     * @param ISummitLocation $location
     * @return void
     */
    public function registerLocation(ISummitLocation $location)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Location')->setTarget($location);
    }

    /**
     * @param ICompany $company
     * @return void
     */
    public function addSponsor(ICompany $company)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Sponsors')->add($company);
    }

    /**
     * @return void
     */
    public function clearAllSponsors()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'Sponsors')->removeAll();
    }

    /**
     * @param ISummitEventType $type
     * @return void
     */
    public function setType(ISummitEventType $type)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Type')->setTarget($type);
    }

    /**
     * @param ISummitType $summit_type
     * @return void
     */
    public function addAllowedSummitType(ISummitType $summit_type)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'AllowedSummitTypes')->add($summit_type);
    }

    /**
     * @return void
     */
    public function clearAllAllowedSummitTypes()
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this, 'AllowedSummitTypes')->removeAll();
    }

    /**
     * @param ISummitEventFeedBack $feedback
     * @return void
     */
    public function addFeedback(ISummitEventFeedBack $feedback)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Feedback')->add($feedback);
    }

    /**
     * @return void
     */
    public function clearAllFeedback()
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Feedback')->removeAll();
    }

    public function getCMSFields()
    {

        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('Title','Title'));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('Description','Description'));
        $f->addFieldToTab('Root.Main', new CheckboxField('AllowFeedBack','Is feedback allowed?'));
        $f->addFieldToTab('Root.Main', new CheckboxField('Published','Is approved?'));
        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));

        $f->addFieldToTab('Root.Main',$date = new DatetimeField('StartDate', 'Start Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldToTab('Root.Main',$date = new DatetimeField('EndDate', 'End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');


        $locations = SummitAbstractLocation::get()
            ->filter('SummitID', $summit_id )
            ->filter('ClassName', array('SummitVenue', 'SummitVenueRoom', 'SummitExternalLocation') );

        $locations_source = array();

        foreach($locations as $l)
        {
            $locations_source[$l->ID] = $l->getFullName();
        }

        $f->addFieldToTab
        (
            'Root.Main',
            $ddl_location = new DropdownField
            (
                'LocationID',
                'Location',
                $locations_source
            )
        );

        $ddl_location->setEmptyString('-- Select a Location --');

        $f->addFieldToTab
        (
            'Root.Main',
            $ddl_location = new DropdownField
            (
                'TypeID',
                'Event Type',
                SummitEventType::get()->filter('SummitID', $summit_id)->map('ID', 'Type')
            )
        );

        $ddl_location->setEmptyString('-- Select a Event Type --');

        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));

        if($this->ID > 0)
        {

            // summits types
            $config = new GridFieldConfig_RelationEditor(100);
            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
            $completer->setSearchList(SummitType::get()->filter('SummitID', $summit_id));
            $summit_types = new GridField('AllowedSummitTypes', 'Summit Types', $this->AllowedSummitTypes(), $config);
            $f->addFieldToTab('Root.Main', $summit_types);

            // sponsors
            $config = new GridFieldConfig_RelationEditor(100);
            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $sponsors = new GridField('Sponsors', 'Sponsors', $this->Sponsors(), $config);
            $f->addFieldToTab('Root.Sponsors', $sponsors);

            // feedback
            $config = new GridFieldConfig_RecordEditor(100);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $feedback = new GridField('Feedback', 'Feedback', $this->Feedback(), $config);
            $f->addFieldToTab('Root.Feedback', $feedback);
        }
        return $f;
    }

    public function publish()
    {
        if($this->Published)
            throw new Exception('Already published Summit Event');

        $start_date = $this->getStartDate();
        $end_date   = $this->getEndDate();

        if(empty($start_date) || empty($end_date))
            throw new Exception('You must define a start/end datetime before publish it');

        $this->Published = true;
        $this->PublishedDate = MySQLDatabase56::nowRfc2822();
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $publish_date = $this->PublishedDate;
        //first time published ...
        if($this->isPublished() && is_null($publish_date))
        {
            $this->unPublish();
            $this->publish();
        }

        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);

        $time_zone_id   = $summit->TimeZone;
        $start_date     = $this->getField('StartDate');
        $end_date       = $this->getField('EndDate');
        $time_zone_list = timezone_identifiers_list();

        if(isset($time_zone_list[$time_zone_id]) && !empty($start_date) && !empty($end_date) && !$this->already_converted_date)
        {
            $utc_timezone      = new DateTimeZone("UTC");
            $time_zone_name = $time_zone_list[$time_zone_id];
            $time_zone   = new \DateTimeZone($time_zone_name);
            $start_date  = new \DateTime($start_date, $time_zone);
            $end_date    = new \DateTime($end_date, $time_zone);

            $start_date->setTimezone($utc_timezone);
            $this->setField('StartDate', $start_date->format("Y-m-d H:i:s"));

            $end_date->setTimezone($utc_timezone);
            $this->setField('EndDate', $end_date->format("Y-m-d H:i:s"));

            $this->already_converted_date = true;
        }
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return  $this->Published;
    }

    /**
     * @return void
     */
    public function unPublish()
    {
        $this->Published = false;
        $this->PublishedDate = null;
    }

    protected function validate()
    {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $summit   = Summit::get()->byID($summit_id);

        if(!$summit){
            return $valid->error('Invalid Summit!');
        }

        $start_date = $this->getStartDate();
        $end_date   = $this->getEndDate();
        if((empty($start_date) || empty($end_date)) && $this->isPublished())
            return $valid->error('To publish this event you must define a start/end datetime!');

        if(!empty($start_date) && !empty($end_date))
        {

            $timezone = $summit->TimeZone;

            if(empty($timezone)){
                return $valid->error('Invalid Summit TimeZone!');
            }

            $start_date = new DateTime($start_date);
            $end_date = new DateTime($end_date);
            if($end_date < $start_date)
                return $valid->error('start datetime must be greather or equal than end datetime!');
            if(!$summit->isEventInsideSummitDuration($this))
                return $valid->error(sprintf('start/end datetime must be between summit start/end datetime! (%s - %s)', $summit->getBeginDate(), $summit->getEndDate()));

            // validate start time/end time and location
            if(!empty($this->LocationID))
            {

            }
        }
        return $valid;
    }

    public function getSpeakers() {
        return new ArrayList();
    }

    /*public function getAtendees() {
        return AssociationFactory::getInstance()->getMany2ManyAssociation($this , 'Attendees');
    }*/
    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->getStartDateOnTimeZone();
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->getEndDateOnTimeZone();
    }
}