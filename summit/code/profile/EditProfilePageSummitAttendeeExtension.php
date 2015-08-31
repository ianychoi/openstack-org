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
class EditProfilePageSummitAttendeeExtension extends Extension
{
    public function onBeforeInit(){
        Config::inst()->update(get_class($this), 'allowed_actions', array
        (
            'attendeeInfoRegistration',
            'SummitAttendeeInfoForm',
            'saveSummitAttendeeInfo',
        ));
    }

    public function getNavActionsExtensions(&$html){
        $view = new SSViewer('EditProfilePage_SummitAttendeeNav');
        $html .= $view->process($this->owner);
    }

    public function getNavMessageExtensions(&$html){
        $view = new SSViewer('EditProfilePage_SummitAttendeeMessage');
        $html .= $view->process($this->owner);
    }

    public function UpcomingSummit()
    {
        return Summit::GetUpcoming();
    }

    public function CurrentSummit()
    {
        $current_summit = Summit::CurrentSummit();
        if(is_null($current_summit))
            $current_summit = $this->UpcomingSummit();
        return $current_summit;
    }


    public function attendeeInfoRegistration(SS_HTTPRequest $request)
    {
        //return $this->owner->customise(array())->renderWith(array('EditProfilePage_attendeeInfoRegistration', 'Page'));
        return $this->owner->getViewer('attendeeInfoRegistration')->process($this->owner);
    }

    public function SummitAttendeeInfoForm()
    {
        if ($CurrentMember = Member::currentUser())
        {
            $form = new SummitAttendeeInfoForm($this->owner, 'SummitAttendeeInfoForm');
            //Populate the form with the current members data
            $attendee = $CurrentMember->getCurrentSummitAttendee();
            if($attendee) $form->loadDataFrom($attendee->data());
            return $form;
        }
    }

    public function saveSummitAttendeeInfo($data, $form)
    {
        if ($CurrentMember = Member::currentUser())
        {
            $attendee = $CurrentMember->getCurrentSummitAttendee();
            return $this->redirect($this->Link('?saved=1'));
        }
        return $this->httpError(403);
    }
}