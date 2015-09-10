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
    private static $db = array
    (
        'Title'        => 'Text',
        'Description'  => 'HTMLText',
        'StartDate'    => 'SS_Datetime',
        'EndDate'      => 'SS_Datetime',
        'Published'     => 'Boolean',
        'PublishedDate' => 'SS_Datetime',
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
    public function getStartDate()
    {
        return $this->getField('StartDate');
    }

    public function getStartDateNice()
    {
        $start_date =  $this->getField('StartDate');
        if(empty($start_date)) return 'TBD';
        return $start_date;
    }


    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->getField('EndDate');
    }

    public function getEndDateNice()
    {
        $end_date  = $this->getField('EndDate');
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
        $f->addFieldToTab('Root.Main', new CheckboxField('Published','Is Approved?'));
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
            $config = new GridFieldConfig_RelationEditor(10);
            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
            $completer->setSearchList(SummitType::get()->filter('SummitID', $summit_id));
            $summit_types = new GridField('AllowedSummitTypes', 'Summit Types', $this->AllowedSummitTypes(), $config);
            $f->addFieldToTab('Root.Main', $summit_types);

            // sponsors
            $config = new GridFieldConfig_RelationEditor(10);
            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $sponsors = new GridField('Sponsors', 'Sponsors', $this->Sponsors(), $config);
            $f->addFieldToTab('Root.Sponsors', $sponsors);

            // feedback
            $config = new GridFieldConfig_RecordEditor(10);
            $config->removeComponentsByType('GridFieldEditButton');
            $sponsors = new GridField('Feedback', 'Feedback', $this->Feedback(), $config);
            $f->addFieldToTab('Root.Feedback', $sponsors);
        }
        return $f;
    }

    public function publish()
    {
        if($this->Published)
            throw new Exception('Already published Summit Event');

        if($this->getStartDate() === 'TBD' || $this->getEndDate() === 'TBD')
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
}