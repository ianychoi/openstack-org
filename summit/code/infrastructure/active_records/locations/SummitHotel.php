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
class SummitHotel extends SummitGeoLocatedLocation implements ISummitHotel
{
    private static $db = array
    (
        'BookingLink' => 'Text',
        'SoldOut' => 'Boolean',
    );

    private static $has_many = array
    ();

    private static $defaults = array
    ();

    private static $has_one = array
    ();

    private static $summary_fields = array
    ();

    private static $searchable_fields = array
    ();

    /**
     * @return bool
     */
    public function isSoldOut()
    {
        return $this->getField('SoldOut');
    }

    /**
     * @return string
     */
    public function getBookingLink()
    {
        return $this->getField('BookingLink');
    }

    public function getCMSFields()
    {
        $f = parent::getCMSFields();
        $f->addFieldToTab('Root.Main', new TextField('BookingLink','Booking Link'));
        $f->addFieldToTab('Root.Main', new CheckboxField('SoldOut','Is SoldOut'));
        return $f;
    }

    public function getTypeName()
    {
        return 'Hotel';
    }
}