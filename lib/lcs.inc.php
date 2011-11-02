<?php
/*
 * $Id: lcs.inc.php 7109 2011-06-04 17:06:50Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

function connect_ldap($l_adresse,$l_port,$l_login,$l_pwd) {
    $ds = @ldap_connect($l_adresse, $l_port);
    if($ds) {
       // On dit qu'on utilise LDAP V3, sinon la V2 par d?faut est utilis? et le bind ne passe pas.
       $norme = @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
       // Acc?s non anonyme
       if ($l_login != '') {
          // On tente un bind
          $b = @ldap_bind($ds, $l_login, $l_pwd);
       } else {
          // Acc?s anonyme
          $b = @ldap_bind($ds);
       }
       if ($b) {
           return $ds;
       } else {
           return false;
       }
    } else {
       return false;
    }
}

function get_lcs_login($elenoet, $statut) {
	global $ds, $lcs_ldap_people_dn;
	if($statut=='eleve') {
		$filtre = "(employeeNumber=".$elenoet.")";
	}
	elseif($statut=='professeur') {
		$filtre = "(employeeNumber=P".$elenoet.")";
	}
	//echo "<p>";
	//echo "filtre=$filtre<br />";
	$result= ldap_search ($ds, $lcs_ldap_people_dn, $filtre);
	if ($result) {
		$info = @ldap_get_entries($ds, $result);
		//if((count($info)==1)&&(isset($info[0]["uid"][0]))) {
		if((isset($info['count']))&&($info['count']==1)&&(isset($info[0]["uid"][0]))) {
			//echo "\$info[0][\"uid\"][0]=".$info[0]["uid"][0]."<br />";
			return $info[0]["uid"][0];
		}
		else {
			if($statut=='eleve') {
				$filtre = "(employeeNumber=".sprintf("%05d",$elenoet).")";
			}
			elseif($statut=='professeur') {
				$filtre = "(employeeNumber=P".sprintf("%05d",$elenoet).")";
			}
			//echo "filtre=$filtre<br />";
			//echo "ldap_search ($ds, $lcs_ldap_people_dn, $filtre)<br />";
			$result= ldap_search ($ds, $lcs_ldap_people_dn, $filtre);
			if ($result) {
				$info = @ldap_get_entries($ds, $result);
				//echo "\count($info)=".count($info)."<br />";
				/*
				echo "<pre>";
				print_r($info);
				echo "</pre>";
				*/
				// On récupère $info[0] et $info['count']
				//if((count($info)==1)&&(isset($info[0]["uid"][0]))) {
				if((isset($info['count']))&&($info['count']==1)&&(isset($info[0]["uid"][0]))) {
					//echo "\$info[0][\"uid\"][0]=".$info[0]["uid"][0]."<br />";
					return $info[0]["uid"][0];
				}
				else {
					//return false;
					return "";
				}
			}
		}
	}
	//echo "</p>";
}

?>