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
class SummitType extends DataObject implements ISummitType
{
    private static $db = array
    (
        'Title'       => 'Text',
        'Description' => 'HTMLText',
        'Audience'    => 'Text',
        'StartDate'   => 'SS_Datetime',
        'EndDate'     => 'SS_Datetime',
    );

    private static $has_many = array
    (
    );

    private static $defaults = array
    (
    );

    private static $has_one = array
    (
        'Summit' => 'Summit'
    );

    private static $summary_fields = array
    (
        'Title',
        'Audience',
        'StartDate',
        'EndDate'
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

    public function getTitle()
    {
        return $this->getField('Title');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getField('Description');
    }

    /**
     * @return string
     */
    public function getAudience()
    {
        return $this->getField('Audience');
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->getField('StartDate');
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->getField('EndDate');
    }

    /**
     * @return int
     */
    public function getDayDuration()
    {
        return 0;
    }

    public function setTitle($title) {
        $this->setField('Title',$title);
    }

    public function setDescription($description) {
        $this->setField('Description',$description);
    }

    public function setAudience($audience) {
        $this->setField('Audience',$audience);
    }

    public function setStartDate($start_date) {
        $this->setField('StartDate',$start_date);
    }

    public function setEndDate($end_date) {
        $this->setField('EndDate',$end_date);
    }

    public function setSummitId($summit_id) {
        $this->setField('SummitID',$summit_id);
    }

    public function getCMSFields()
    {

        $summit_id = isset($_REQUEST['SummitID']) ? $_REQUEST['SummitID'] : $this->SummitID;

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('Title','Title'));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('Description','Description'));
        $f->addFieldToTab('Root.Main', new TextField('Audience','Audience'));

        $f->addFieldToTab('Root.Main',$date = new DatetimeField('StartDate', 'Start Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldToTab('Root.Main',$date = new DatetimeField('EndDate', 'End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));

        return $f;
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

        if((empty($start_date) || empty($end_date)))
            return $valid->error('You must define a start/end datetime!');

        if(!empty($start_date) && !empty($end_date))
        {
            $start_date = new DateTime($start_date);
            $end_date   = new DateTime($end_date);

            if($end_date < $start_date)
                return $valid->error('start datetime must be greather or equal than end datetime!');
            if(!$summit->isEventInsideSummitDuration($this))
                return $valid->error(sprintf('start/end datetime must be between summit start/end datetime! (%s - %s)', $summit->getBeginDate(), $summit->getEndDate()));

        }
        return $valid;
    }

}