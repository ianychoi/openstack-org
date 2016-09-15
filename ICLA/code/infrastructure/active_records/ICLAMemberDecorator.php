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
/**
 * Class ICLAMemberDecorator
 */
final class ICLAMemberDecorator
	extends DataExtension
	implements ICLAMember, PermissionProvider

{

	//Add extra database fields

	private static $db = array(
	);

	private static $defaults = array(

	);

    private static $has_many = array(
        'GerritUsers' => 'GerritUser'
    );

	private static $belongs_many_many = array(
		'Teams' => 'Team'
	);

    /**
     * @return bool
     */
    public function isGerritUser(){
        return $this->owner->GerritUsers()->count() > 0;
    }

    /**
     * @param string $gerrit_id
     * @param string $email
     * @return GerritUser|null
     */
    public function addGerritUser($gerrit_id, $email){
        if($this->owner->GerritUsers()->filter('AccountID', $gerrit_id)->count() != 0) return null;
        $gerrit_user             = new GerritUser();
        $gerrit_user->AccountID  = $gerrit_id;
        $gerrit_user->Email      = $email;
        $gerrit_user->write();
        $this->owner->GerritUsers()->add($gerrit_user);
        return $gerrit_user;
    }

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->owner->getField('ID');
	}

	function getAdminPermissionSet(array &$res){

		$companyId = $_REQUEST["CompanyId"];

		if(isset($companyId) && is_numeric($companyId) && $companyId > 0){
			// user could be ccla admin of only one company and company must have at least one team set
			$ccla_group = Group::get()->filter('Code',ICLAMember::CCLAGroupSlug)->first();
			if(!$ccla_group) return;
			$query_groups = new SQLQuery();
			$query_groups->addSelect("GroupID");
			$query_groups->addFrom("Company_Administrators");
			$query_groups->addWhere("MemberID = {$this->owner->ID} AND CompanyID <> {$companyId} AND GroupID =  {$ccla_group->ID} ");

			$groups = $query_groups->execute()->keyedColumn();

			$company = Company::get()->byID($companyId);
			if(count($groups) === 0 && $company->isICLASigned())
				array_push($res, 'CCLA_ADMIN');
		}

	}

	function providePermissions() {
		return array(
			ICLAMember::CCLAPermissionSlug => array(
				'name'     => 'CCLA Company Admin',
				'category' => 'Company Management',
				'help'     => 'Allows to manage CCLA Company Members and Teams',
				'sort'     => 0
			),
		);
	}

	/**
	 * @return bool
	 */
	function isCCLAAdmin(){
		$managed_companies = $this->owner->ManagedCompanies();
		foreach($managed_companies as $company){
			$groups = $company->getAdminGroupsByMember($this->getIdentifier());
			if(is_null($groups) || count($groups)==0) continue;
			if( array_key_exists(ICLAMember::CCLAGroupSlug , $groups)){
				$company = $this->getManagedCCLACompany();
				if(!$company) return false;
				return $company->isICLASigned();
			}
		}
		return false;
	}

	/**
	 * @return ICLACompany
	 */
	function getManagedCCLACompany(){
		$managed_companies = $this->owner->ManagedCompanies();
		foreach($managed_companies as $company){
			$groups = $company->getAdminGroupsByMember($this->getIdentifier());
			if(is_null($groups) || count($groups)==0) continue;
			if( array_key_exists(ICLAMember::CCLAGroupSlug, $groups)){
				return $company;
			}
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function hasSignedCLA()
	{
		return $this->isGerritUser();
	}

    public function onBeforeDelete()
    {
        foreach($this->owner->GerritUsers() as $gerritUser)
        {
            $gerritUser->delete();
        }
    }
}