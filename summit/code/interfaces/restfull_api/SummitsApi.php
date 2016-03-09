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

/**
 * Class SummitsApi
 */
final class SummitsApi extends AbstractRestfulJsonApi
{

    const ApiPrefix = 'api/v1/summits';

    /**
     * @var IEntityRepository
     */
    private $sponsorship_add_on_repository;

    /**
     * @var IEntityRepository
     */
    private $sponsorship_package_repository;

    /**
     * @var ISummitPackagePurchaseOrderManager
     */
    private $package_purchase_order_manager;

    /**
     * @var ISummitRepository
     */
    private $summit_repository;

    public function __construct(
        IEntityRepository $sponsorship_package_repository,
        IEntityRepository $sponsorship_add_on_repository,
        ISummitPackagePurchaseOrderManager $package_purchase_order_manager,
        ISummitRepository $summit_repository
    ) {
        parent::__construct();

        $this->sponsorship_add_on_repository = $sponsorship_add_on_repository;
        $this->sponsorship_package_repository = $sponsorship_package_repository;
        $this->package_purchase_order_manager = $package_purchase_order_manager;
        $this->summit_repository              = $summit_repository;

        $this_var = $this;

        $this->addBeforeFilter('getAllSponsorshipAddOnsBySummit', 'check_own_request',
            function ($request) use ($this_var) {
                if (!$this_var->checkOwnAjaxRequest()) {
                    return $this_var->permissionFailure();
                }
            });

        $this->addBeforeFilter('getAllSponsorshipAddOnsBySummit', 'check_own_request2',
            function ($request) use ($this_var) {
                if (!$this_var->checkOwnAjaxRequest()) {
                    return $this_var->permissionFailure();
                }
            });

        $this->addBeforeFilter('approvePurchaseOrder', 'check_approve', function ($request) use ($this_var) {
            if (!$this_var->checkAdminPermissions($request)) {
                return $this_var->permissionFailure();
            }
        });

        $this->addBeforeFilter('rejectPurchaseOrder', 'check_reject', function ($request) use ($this_var) {
            if (!$this_var->checkAdminPermissions($request)) {
                return $this_var->permissionFailure();
            }
        });

    }

    public function checkAdminPermissions($request)
    {
        return Permission::check("SANGRIA_ACCESS");
    }

    protected function isApiCall()
    {
        $request = $this->getRequest();
        if (is_null($request)) {
            return false;
        }

        return strpos(strtolower($request->getURL()), self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    protected function authenticate()
    {
        return true;
    }

    static $url_handlers = array(
        'GET $SUMMIT_ID/add-ons'                                  => 'getAllSponsorshipAddOnsBySummit',
        'GET $SUMMIT_ID/packages'                                 => 'getAllSponsorshipPackagesBySummit',
        'GET $SUMMIT_ID/tags'                                     => 'getTags',
        'GET $SUMMIT_ID/companies'                                => 'getCompanies',
        'GET $SUMMIT_ID/sponsors'                                 => 'getSponsors',
        'GET $SUMMIT_ID/speakers'                                 => 'getSpeakers',
        '$SUMMIT_ID/schedule'                                     => 'handleSchedule',
        '$SUMMIT_ID/events'                                       => 'handleEvents',
        '$SUMMIT_ID/attendees'                                    => 'handleAttendees',
        '$SUMMIT_ID/members'                                      => 'handleMembers',
        '$SUMMIT_ID/reports'                                      => 'handleReports',
        'PUT packages/purchase-orders/$PURCHASE_ORDER_ID/approve' => 'approvePurchaseOrder',
        'PUT packages/purchase-orders/$PURCHASE_ORDER_ID/reject'  => 'rejectPurchaseOrder',
    );

    static $allowed_actions = array(
        'getAllSponsorshipAddOnsBySummit',
        'getAllSponsorshipPackagesBySummit',
        'approvePurchaseOrder',
        'rejectPurchaseOrder',
        'handleSchedule',
        'handleEvents',
        'handleAttendees',
        'handleMembers',
        'handleReports',
        'getTags',
        'getSponsors',
        'getCompanies',
        'getSpeakers',
    );

    // this is called when typing a tag name to add as a tag on edit event
    public function getTags(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $query        = Convert::raw2sql($query_string['query']);
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $tags = DB::query("SELECT T.ID AS id, T.Tag AS name FROM Tag AS T
                                    WHERE T.Tag LIKE '{$query}%'
                                    ORDER BY T.Tag LIMIT 10;");

            $json_array = array();
            foreach ($tags as $tag) {
                $json_array[] = $tag;
            }

            echo json_encode($json_array);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    // this is called when typing a sponsor name to add as a tag on edit event
    public function getSponsors(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $query        = Convert::raw2sql($query_string['query']);
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $sponsors = DB::query("SELECT C.ID AS id, C.Name AS name FROM Company AS C
                                    WHERE C.Name LIKE '{$query}%'
                                    ORDER BY C.Name LIMIT 10;");

            $json_array = array();
            foreach ($sponsors as $sponsor) {
                $json_array[] = $sponsor;
            }

            echo json_encode($json_array);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    // this is called when typing a Speakers name to add as a tag on edit event
    public function getSpeakers(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $term         = Convert::raw2sql($query_string['query']);
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $slug1 = IFoundationMember::CommunityMemberGroupSlug;
            $slug2 = IFoundationMember::FoundationMemberGroupSlug;

            $query = <<<SQL
SELECT CONCAT(M.ID,'_',IFNULL(PS.ID , 0)) AS unique_id,
M.ID AS member_id ,
M.ID AS id, CONCAT(M.FirstName,' ',M.Surname,' (',M.ID,')') AS name,
IFNULL(PS.ID , 0) AS speaker_id
FROM Member AS M
LEFT JOIN PresentationSpeaker AS PS ON PS.MemberID = M.ID
WHERE
(
  M.FirstName LIKE '%{$term}%' OR
  M.Surname LIKE '%{$term}%' OR
  M.Email LIKE '%{$term}%' OR
  CONCAT(M.FirstName,' ',M.Surname) LIKE '%{$term}%'
)
AND
EXISTS
(
  SELECT 1 FROM Group_Members AS GM
  INNER JOIN `Group` AS G ON G.ID = GM.GroupID
  WHERE
  GM.MemberID = M.ID
  AND
  (
    G.Code = '{$slug1}'
    OR
    G.Code = '{$slug2}'
  )
)
ORDER BY M.FirstName, M.Surname
LIMIT 25;
SQL;


            $speakers = DB::query($query);

            $json_array = array();
            foreach ($speakers as $s) {

                $json_array[] = $s;
            }

            echo json_encode($json_array);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getCompanies(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $query        = Convert::raw2sql($query_string['query']);
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $orgs = DB::query(" SELECT O.ID AS id, O.Name AS name FROM Org AS O
                                WHERE O.Name LIKE '{$query}%'
                                ORDER BY O.Name LIMIT 10;");

            $json_array = array();
            foreach ($orgs as $org) {

                $json_array[] = $org;
            }

            echo json_encode($json_array);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getAllSponsorshipAddOnsBySummit(SS_HTTPRequest $request)
    {
        try {
            $response = $this->loadJSONResponseFromCache($request);
            if(!is_null($response)) return $response;

            $summit_id = (int)$request->param('SUMMIT_ID');
            $query = new QueryObject(new SummitAddOn);
            $query->addAndCondition(QueryCriteria::equal('SummitSponsorPageID', $summit_id));
            $query->addOrder(QueryOrder::asc("Order"));
            list($list, $count) = $this->sponsorship_add_on_repository->getAll($query, 0, 999999);
            $res = array();
            foreach ($list as $add_on) {
                array_push($res, SummitAddOnAssembler::toArray($add_on));
            }

            return $this->saveJSONResponseToCache($request, $res)->ok($res);
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::WARN);

            return $this->serverError();
        }
    }

    public function getAllSponsorshipPackagesBySummit(SS_HTTPRequest $request)
    {
        try {
            $response = $this->loadJSONResponseFromCache($request);
            if(!is_null($response)) return $response;
            $query = new QueryObject(new SummitPackage());
            $summit_id = (int)$request->param('SUMMIT_ID');
            $query->addAndCondition(QueryCriteria::equal('SummitSponsorPageID', $summit_id));
            $query->addOrder(QueryOrder::asc("Order"));
            list($list, $count) = $this->sponsorship_package_repository->getAll($query, 0, 999999);
            $res = array();
            foreach ($list as $package) {
                array_push($res, SummitPackageAssembler::toArray($package));
            }

            return $this->saveJSONResponseToCache($request, $res)->ok($res);
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::WARN);

            return $this->serverError();
        }
    }

    public function approvePurchaseOrder()
    {
        try {
            $order_id = (int)$this->request->param('PURCHASE_ORDER_ID');
            $this->package_purchase_order_manager->approvePurchaseOrder($order_id,
                new ApprovedPurchaseOrderEmailMessageSender);

            return $this->updated();
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::ERR);

            return $this->notFound($ex1->getMessage());
        } catch (EntityValidationException $ex2) {
            SS_Log::log($ex2, SS_Log::NOTICE);

            return $this->validationError($ex2->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);

            return $this->serverError();
        }
    }

    public function rejectPurchaseOrder()
    {
        try {
            $order_id = (int)$this->request->param('PURCHASE_ORDER_ID');
            $this->package_purchase_order_manager->rejectPurchaseOrder($order_id,
                new RejectedPurchaseOrderEmailMessageSender);

            return $this->updated();
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::ERR);

            return $this->notFound($ex1->getMessage());
        } catch (EntityValidationException $ex2) {
            SS_Log::log($ex2, SS_Log::NOTICE);

            return $this->validationError($ex2->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);

            return $this->serverError();
        }
    }

    public function handleSchedule(SS_HTTPRequest $request)
    {
        $api = SummitAppScheduleApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }

    public function handleEvents(SS_HTTPRequest $request)
    {
        $api = SummitAppEventsApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }

    public function handleAttendees(SS_HTTPRequest $request)
    {
        $api = SummitAppAttendeesApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }

    public function handleMembers(SS_HTTPRequest $request)
    {
        $api = SummitAppMembersApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }

    public function handleReports(SS_HTTPRequest $request)
    {
        $api = SummitAppReportsApi::create();
        return $api->handleRequest($request, DataModel::inst());
    }
}