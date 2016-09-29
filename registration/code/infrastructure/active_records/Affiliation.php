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
class Affiliation extends DataObject {
    static $db = array(
        'StartDate' => 'Date',
        'EndDate' => 'Date',
        'JobTitle'=>'Text',
        'Role'=>'Text',
        'Current'=>'Boolean'
    );

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator= new RequiredFields(array('StartDate'));
        return $validator;
    }

    static $has_one = array(
        'Member' => 'Member',
        'Organization'=>'Org',
    );

    public function getDuration(){
        $end = $this->Current==true?'(Current)':"To {$this->EndDate}";
        return "From {$this->StartDate} {$end}";
    }
}