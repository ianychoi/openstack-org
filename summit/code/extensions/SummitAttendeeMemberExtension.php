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
class SummitAttendeeMemberExtension extends DataExtension
{
    private static $has_many = array
    (
        'SummitAttendance' => 'SummitAttendee'
    );


    public function getCurrentSummitAttendee()
    {
        $current_summit = Summit::CurrentSummit();
        if($current_summit)
        {
            return $this->owner->SummitAttendance()->filter('SummitID', $current_summit->ID)->first();
        }
        return $this->getUpcomingSummitAttendee();
    }

    public function getUpcomingSummitAttendee()
    {
        $upcoming_summit = Summit::GetUpcoming();
        if($upcoming_summit)
        {
            return $this->owner->SummitAttendance()->filter('SummitID', $upcoming_summit->ID)->first();
        }
        return null;
    }

}