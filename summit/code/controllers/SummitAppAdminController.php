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
class SummitAppAdminController extends Page_Controller
{

    public function init()
    {
        parent::init();
    }

    private static $url_segment = 'summit-admin';

    private static $allowed_actions = array(
        'directory',
        'dashboard',
        'publishedEvents',
        'pendingEvents',
        'editEvent',
        'presentationLists',
        'editPresentationList',
    );

    private static $url_handlers = array (
        '$SummitID!/dashboard' => 'dashboard',
        '$SummitID!/events/published' => 'publishedEvents',
        '$SummitID!/events/unpublished' => 'pendingEvents',
        '$SummitID!/events/presentation-lists/$PresentationListId!'  => 'editPresentationList',
        '$SummitID!/events/presentation-lists'  => 'presentationLists',
        '$SummitID!/events/$EventID!'  => 'editEvent',
    );

    /**
     * Ensure all root requests go to login
     * @return SS_HTTPResponse
     */
    public function index()
    {
        if(Member::currentUser())
            return $this->redirect($this->Link('directory'));
        return $this->redirect('/Security/login/?BackURL=/summit-admin');
    }

    public function Link($action = null)
    {
        return Controller::join_links($this->config()->url_segment, $action);
    }

    public function directory()
    {
        $summits = Summit::get();
        return $this->getViewer('directory')->process
        (
          $this->customise
          (
              array
              (
                  'Summits' => $summits
              )
          )
        );
    }

    public function publishedEvents(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');

        $events = $summit->Events()->filter('Published', true);

        return $this->getViewer('publishedEvents')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit,
                    'Events' => $events
                )
            )
        );
    }

    public function pendingEvents(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');

        $events = $summit->Events()->filter('Published', false);

        return $this->getViewer('pendingEvents')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit,
                    'Events' => $events
                )
            )
        );
    }

    public function presentationLists(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');

        $events = $summit->Events()->filter('Published', false);

        return $this->getViewer('presentationLists')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit,
                )
            )
        );
    }

    public function editPresentationList(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');


        return $this->getViewer('editPresentationList')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit,
                )
            )
        );
    }


    public function dashboard(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        return $this->getViewer('dashboard')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit
                )
            )
        );
    }

    public function editEvent(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);
        $event_id = intval($request->param('EventID'));

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        return $this->getViewer('EditEvent')->process
        (
            $this->customise
            (
                array
                (
                    'Summit' => $summit,
                    'Event' => null
                )
            )
        );
    }
}