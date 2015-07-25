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
}