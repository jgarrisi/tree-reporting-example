<?php namespace App\Services;

class LDAP
{
    public static function userHasAccess($username)
    {
		return in_array("uid=" . $username . ",ou=People,o=hp.com", LDAP::getAccessList());
    }

    private static function getAccessList()
    {
        global $config;

	    // Active Directory server
    	$ldap_host = "ldap.hp.com";

	    // Active Directory DN
	    $ldap_dn = "ou=Groups,o=hp.com";

	    // Connect to AD
	    $ldap = ldap_connect($ldap_host) or die('Could not connect to LDAP');
	        ldap_bind($ldap) or die('Could not bind to LDAP');

	    // Search AD
	    $results = ldap_search($ldap, $ldap_dn, 'cn='.'day1-it-smo-rip-data-entry');
        $entries = ldap_get_entries($ldap, $results);
        array_shift($entries[0]['member']);

        $accessList = $entries[0]['member'];

        return $accessList;
    }
}
