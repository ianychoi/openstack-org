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
class OnSitePhoneForm extends BootstrapForm
{

    function __construct($controller, $name, PresentationSpeakerSummitAssistanceConfirmationRequest $request)
    {

        $PhoneField = new TextField('OnSitePhoneNumber', 'Your OnSite Phone Number for ' . $request->Summit()->Title . ':');
        $RegisteredField = new CheckboxField('RegisteredForSummit', 'I have registered for the summit using the confirmation code sent in the email.');

        $SpeakerHashField = new HiddenField('RequestID', "RequestID", $request->ID);

        $fields = new FieldList(
            $PhoneField,
            $RegisteredField,
            $SpeakerHashField
        );

        $submitButton = new FormAction('doSavePhoneNumber', 'Save');

        $actions = new FieldList(
            $submitButton
        );


        $validator = new RequiredFields('OnSitePhoneNumber');
        parent::__construct($controller, $name, $fields, $actions, $validator);

    }


    function doSavePhoneNumber($data, $form)
    {
        if(!isset($data['RequestID'])) throw new InvalidArgumentException('missing RequestID!');
        $request_id = intval(Convert::raw2sql($data['RequestID']));
        $request = Session::get('Current.PresentationSpeakerSummitAssistanceConfirmationRequest');
        if(is_null($request) || $request->ID !== $request_id) throw new InvalidArgumentException('invalid RequestID!');
        $request->OnSitePhoneNumber   = Convert::raw2sql($data['OnSitePhoneNumber']);
        $request->RegisteredForSummit = Convert::raw2sql($data['RegisteredForSummit']);
        $request->write();
        return Controller::curr()->redirect(Controller::curr()->Link() . 'Thanks/');
      }

}