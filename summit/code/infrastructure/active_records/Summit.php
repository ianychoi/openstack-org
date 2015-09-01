<?php

/**
 * Copyright 2014 Openstack Foundation
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
final class Summit extends DataObject implements ISummit
{

    private static $db = array
    (
        'Name'                        => 'Varchar(255)',
        'Title'                       => 'Varchar',
        'SummitBeginDate'             => 'SS_Datetime',
        'SummitEndDate'               => 'SS_Datetime',
        'SubmissionBeginDate'         => 'SS_Datetime',
        'SubmissionEndDate'           => 'SS_Datetime',
        'VotingBeginDate'             => 'SS_Datetime',
        'VotingEndDate'               => 'SS_Datetime',
        'SelectionBeginDate'          => 'SS_Datetime',
        'SelectionEndDate'            => 'SS_Datetime',
        'Active'                      => 'Boolean',
        'DateLabel'                   => 'Varchar',
        'Link'                        => 'Varchar',
        'RegistrationLink'            => 'Text',
        'ComingSoonBtnText'           => 'Text',
        // https://www.eventbrite.com
        'ExternalEventId'             => 'Text',
    );

    private static $has_one = array
    (

    );

    private static $has_many = array
    (
        'Presentations'                => 'Presentation',
        'Categories'                   => 'PresentationCategory',
        'Speakers'                     => 'PresentationSpeaker',
        'Locations'                    => 'SummitAbstractLocation',
        'Types'                        => 'SummitType',
        'EventTypes'                   => 'SummitEventType',
        'Events'                       => 'SummitEvent',
        'Attendees'                    => 'SummitAttendee',
        'SummitTicketTypes'            => 'SummitTicketType',
        'SummitRegistrationPromoCodes' => 'SummitRegistrationPromoCode',
    );

    private static $summary_fields = array
    (
        'Title'  => 'Title',
        'Status' => 'Status',
    );

    private static $searchable_fields = array
    (
    );

    public static function get_active()
    {
        $summit = Summit::get()->filter
        (
            array
            (
                'Active' => true
            )
        )->first();

        return $summit ?: Summit::create();
    }

    public function checkRange($key)
    {
        $beginField = "{$key}BeginDate";
        $endField   = "{$key}EndDate";

        if (!$this->hasField($beginField) || !$this->hasField($endField)) return false;

        return (time() > $this->obj($beginField)->format('U')) && (time() < $this->obj($endField)->format('U'));
    }


    public function getStatus()
    {
        if (!$this->Active) return "INACTIVE";

        if ($this->checkRange("Submission")) return "ACCEPTING SUBMISSIONS";
        if ($this->checkRange("Voting")) return "COMMUNITY VOTING";
        if ($this->checkRange("Selection")) return "TRACK CHAIR SELECTION";
        if ($this->checkRange("Summit")) return "SUMMIT IS ON";

        return "DRAFT";
    }


    public function getTitle(){
        $title = $this->getField('Title');
        $name  = $this->getField('Name');
        return empty($title)? $name : $title;
    }

    function TalksByMemberID($memberID)
    {

        $SpeakerList = new ArrayList();

        // Pull any talks that belong to this Summit and are owned by member
        $talksMemberOwns = $this->Talks("`OwnerID` = " . $memberID . " AND `SummitID` = " . $this->ID);
        $SpeakerList->merge($talksMemberOwns);

        // Now pull any talks that belong to this Summit and the member is listed as a speaker
        $speaker = Speaker::get()->filter('memberID', $memberID)->first();
        if ($speaker) {
            $talksMemberIsASpeaker = $speaker->TalksBySummitID($this->ID);

            // Now merge and de-dupe the lists
            $SpeakerList->merge($talksMemberIsASpeaker);
            $SpeakerList->removeDuplicates('ID');
        }

        return $SpeakerList;
    }

    /*
     * @return int
     */
    public static function CurrentSummitID()
    {
        $current = self::CurrentSummit();
        return is_null($current) ? 0 : $current->ID;
    }

    /**
     * @return ISummit
     */
    public static function CurrentSummit()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        return Summit::get()->filter(array(
            'SummitBeginDate:LessThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'SummitEndDate:GreaterThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'Active' => 1
        ))->first();
    }

    /**
     * @return bool
     */
    public function IsCurrent()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        $start = new \DateTime($this->SummitBeginDate, new DateTimeZone('UTC'));
        $end = new \DateTime($this->SummitEndDate, new DateTimeZone('UTC'));
        return $this->Active && $start <= $now && $end >= $now;
    }

    public function IsUpComing()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        $start = new \DateTime($this->SummitBeginDate, new DateTimeZone('UTC'));
        $end = new \DateTime($this->SummitEndDate, new DateTimeZone('UTC'));
        return $this->Active && $start >= $now && $end >= $now;
    }

    public static function GetUpcoming()
    {
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        return Summit::get()->filter(array(
            'SummitBeginDate:GreaterThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'SummitEndDate:GreaterThanOrEqual' => $now->format('Y-m-d H:i:s'),
            'Active' => 1
        ))->first();
    }


    public function onAfterWrite()
    {
        parent::onAfterWrite();

        /*if ($this->Active) {
            foreach (Presentation::get()->exclude('ID', $this->ID) as $p) {
                $p->Active = false;
                $p->write();
            }
        }*/
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getField('Name');
    }

    /**
     * @return DateTime
     */
    public function getBeginDate()
    {
        return $this->getField('SummitBeginDate');
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->getField('SummitEndDate');
    }

    /**
     * @return DateTime
     */
    public function getSubmissionBeginDate()
    {
        return $this->getField('SubmissionBeginDate');
    }

    /**
     * @return DateTime
     */
    public function getSubmissionEndDate()
    {
        return $this->getField('SubmissionEndDate');
    }

    /**
     * @return DateTime
     */
    public function getVotingBeginDate()
    {
        return $this->getField('VotingBeginDate');
    }

    /**
     * @return DateTime
     */
    public function getVotingEndDate()
    {
        return $this->getField('VotingEndDate');
    }

    /**
     * @return DateTime
     */
    public function getSelectionBeginDate()
    {
        return $this->getField('SelectionBeginDate');
    }

    /**
     * @return DateTime
     */
    public function getSelectionEndDate()
    {
        return $this->getField('SelectionEndDate');
    }

    public function getEvents() {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Events');
    }

    /**
     * @return ISummitEventType[]
     */
    public function getEventTypes()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'EventTypes');
    }

    /**
     * @param ISummitEventType $type
     * @return void
     */
    public function addEventType(ISummitEventType $event_type)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'EventTypes')->add($event_type);
    }

    /**
     * @return ISummitType[]
     */
    public function getTypes()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Types');
    }

    /**
     * @param ISummitType $type
     * @return void
     */
    public function addType(ISummitType $type)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Types')->add($type);
    }

    /**
     * @return void
     */
    public function clearAllTypes()
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Types')->removeAll();
    }

    /**
     * @return ISummitAirport[]
     */
    public function getAirports()
    {
        $query = new QueryObject(new SummitAirport);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitAirport'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->toArray();
    }

    /**
     * @param ISummitAirport $airport
     * @return void
     */
    public function addAirport(ISummitAirport $airport)
    {
        $query = new QueryObject(new SummitAirport);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitAirport'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->add($airport);
    }

    /**
     * @return void
     */
    public function clearAllAirports()
    {
        $query = new QueryObject(new SummitAirport);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitAirport'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->removeAll();
    }

    /**
     * @return ISummitHotel[]
     */
    public function getHotels()
    {
        $query = new QueryObject(new SummitHotel);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitHotel'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->toArray();
    }

    /**
     * @param ISummitHotel $hotel
     * @return void
     */
    public function addHotel(ISummitHotel $hotel)
    {
        $query = new QueryObject(new SummitHotel);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitHotel'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->add($hotel);
    }

    /**
     * @return void
     */
    public function clearAllHotels()
    {
        $query = new QueryObject(new SummitHotel);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitHotel'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->removeAll();
    }

    /**
     * @return ISummitVenue[]
     */
    public function getVenues()
    {
        $query = new QueryObject(new SummitVenue);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitVenue'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->toArray();
    }

    /**
     * @param ISummitVenue $venue
     * @return void
     */
    public function addVenue(ISummitVenue $venue)
    {
        $query = new QueryObject(new SummitVenue);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitVenue'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->add($venue);
    }

    /**
     * @return void
     */
    public function clearAllVenues()
    {
        $query = new QueryObject(new SummitVenue);
        $query->addAndCondition(QueryCriteria::equal('ClassName','SummitVenue'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Locations', $query)->removeAll();
    }

    // CMS admin UI
    public function getCMSFields()
    {

        $_REQUEST['SummitID'] = $this->ID;

        $f = new FieldList(
            $rootTab = new TabSet("Root",   $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main',new TextField('Title','Title'));
        $f->addFieldToTab('Root.Main',$link = new TextField('Link','Summit Page Link'));
        $link->setDescription('The link to the site page for this summit. Eg: <em>/summit/vancouver-2015/</em>');
        $f->addFieldToTab('Root.Main',new CheckboxField('Active','This is the active summit'));
        $f->addFieldToTab('Root.Main',$date_label = new TextField('DateLabel','Date label'));
        $date_label->setDescription('A readable piece of text representing the date, e.g. <em>May 12-20, 2015</em> or <em>December 2016</em>');

        $f->addFieldToTab('Root.Main',$registration_link = new TextField('RegistrationLink', 'Registration Link'));
        $registration_link->setDescription('Link to the site where tickets can be purchased.');

        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SummitBeginDate', 'Summit Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SummitEndDate', 'Summit End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SubmissionBeginDate', 'Submission Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SubmissionEndDate', 'Submission End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('VotingBeginDate', 'Voting Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('VotingEndDate', 'Voting End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SelectionBeginDate', 'Selection Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main',$date = new DatetimeField('SelectionEndDate', 'Selection End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldToTab('Root.Main',new TextField('ComingSoonBtnText', 'Coming Soon Btn Text'));

        $f->addFieldToTab('Root.Main',new TextField('ExternalEventId', 'Eventbrite Event Id'));


        $config = new GridFieldConfig_RelationEditor(10);
        $categories = new GridField('Categories','Presentation Categories',$this->Categories(), $config);
        $f->addFieldToTab('Root.Presentation Categories', $categories);

        // locations

        $config = GridFieldConfig_RecordEditor::create();
        $config->removeComponentsByType('GridFieldAddNewButton');
        $multi_class_selector = new GridFieldAddNewMultiClass();
        $multi_class_selector->setClasses
        (
            array
            (
              'SummitVenue'        => 'Venue',
              'SummitHotel'        => 'Hotel',
              'SummitAirport'      => 'Airport',
            )
        );
        $config->addComponent($multi_class_selector);
        $config->addComponent($sort = new GridFieldSortableRows('Order'));
        $gridField = new GridField('Locations', 'Locations', $this->Locations()->where("ClassName <> 'SummitVenueRoom' "), $config);
        $f->addFieldToTab('Root.Locations', $gridField);

        // types

        $config = GridFieldConfig_RecordEditor::create();
        $gridField = new GridField('Types', 'Types', $this->Types(), $config);
        $f->addFieldToTab('Root.Types', $gridField);

        // event types
        $config = GridFieldConfig_RecordEditor::create();
        $gridField = new GridField('EventTypes', 'EventTypes', $this->EventTypes(), $config);
        $f->addFieldToTab('Root.EventTypes', $gridField);

        //schedule

        $config = GridFieldConfig_RecordEditor::create();
        $config->addComponent(new GridFieldAjaxRefresh(1000,false));
        $config->removeComponentsByType('GridFieldDeleteAction');
        $gridField = new GridField('Schedule', 'Schedule', $this->Events()->filter('Published', true)->sort
        (
            array
            (
                'StartDate' => 'ASC',
                'EndDate' => 'ASC'
            )
        ) , $config);
        $f->addFieldToTab('Root.Schedule', $gridField);
        $config->addComponent(new GridFieldPublishSummitEventAction);

        // events

        $config = GridFieldConfig_RecordEditor::create();
        $config->addComponent(new GridFieldPublishSummitEventAction);
        $config->addComponent(new GridFieldAjaxRefresh(1000,false));
        $gridField = new GridField('Events', 'Events', $this->Events()->filter('ClassName','SummitEvent') , $config);
        $f->addFieldToTab('Root.Events', $gridField);

        //track selection list presentations

       $result = DB::query("SELECT DISTINCT SummitEvent.*, Presentation.*
FROM SummitEvent
INNER JOIN Presentation ON Presentation.ID = SummitEvent.ID
INNER JOIN SummitSelectedPresentation ON SummitSelectedPresentation.PresentationID = Presentation.ID
INNER JOIN SummitSelectedPresentationList ON SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID
WHERE(ListType = 'Group') AND (SummitEvent.ClassName IN ('Presentation')) AND  (SummitEvent.SummitID = 5)");

        $presentations = new ArrayList();
        foreach($result as $row)
        {
            $presentations->add(new Presentation($row));
        }

        $config = GridFieldConfig_RecordEditor::create();
        $config->addComponent(new GridFieldPublishSummitEventAction);
        $config->addComponent(new GridFieldAjaxRefresh(1000, false));
        $config->removeComponentsByType('GridFieldAddNewButton');
        $gridField = new GridField('TrackChairs', 'TrackChairs Selection Lists',$presentations  , $config);
        $f->addFieldToTab('Root.TrackChairs Selection Lists', $gridField);


        // attendees

        $config = GridFieldConfig_RecordEditor::create();
        $gridField = new GridField('Attendees', 'Attendees', $this->Attendees(), $config);
        $f->addFieldToTab('Root.Attendees', $gridField);


        //tickets types

        $config = GridFieldConfig_RecordEditor::create();
        $gridField = new GridField('SummitTicketTypes', 'Ticket Types', $this->SummitTicketTypes(), $config);
        $f->addFieldToTab('Root.TicketTypes', $gridField);

        // promo codes

        $config    = GridFieldConfig_RecordEditor::create(25);
        $config->removeComponentsByType('GridFieldAddNewButton');
        $multi_class_selector = new GridFieldAddNewMultiClass();


        $multi_class_selector->setClasses
        (
            array
            (
                'SpeakerSummitRegistrationPromoCode' => 'Speaker Promo Code',
            )
        );

        $config->addComponent($multi_class_selector);


        $promo_codes = new GridField('SummitRegistrationPromoCodes','Registration Promo Codes', $this->SummitRegistrationPromoCodes(), $config);
        $f->addFieldToTab('Root.RegistrationPromoCodes', $promo_codes);

        // speakers

        $config = GridFieldConfig_RecordEditor::create();
        $gridField = new GridField('Speakers', 'Speakers', $this->Speakers(), $config);
        $f->addFieldToTab('Root.Speakers', $gridField);

        // presentations

        $config = GridFieldConfig_RecordEditor::create();
        $config->addComponent(new GridFieldPublishSummitEventAction);
        $config->addComponent(new GridFieldAjaxRefresh(1000, false));
        $gridField = new GridField('Presentations', 'Presentations', $this->Presentations()->filter
        (
            'Status', 'Received'
        ), $config);
        $f->addFieldToTab('Root.Presentations', $gridField);

        return $f;

    }

    /**
     * @param SummitMainInfo $info
     * @return void
     */
    function registerMainInfo(SummitMainInfo $info)
    {
        $this->Name = $info->getName();
        $this->SummitBeginDate = $info->getStartDate();
        $this->SummitEndDate = $info->getEndDate();
    }

    public function isEventInsideSummitDuration(ISummitEvent $summit_event)
    {
        $event_start_date = new DateTime($summit_event->getStartDate());
        $event_end_date   = new DateTime($summit_event->getEndDate());
        $summit_start_date = new DateTime($this->getBeginDate());
        $summit_end_date = new DateTime($this->getEndDate());

        return  $event_start_date >= $summit_start_date && $event_start_date <= $summit_end_date &&
        $event_end_date <= $summit_end_date && $event_end_date >= $event_start_date;
    }

    public function isAttendeesRegistrationOpened()
    {
        return true;
    }
}