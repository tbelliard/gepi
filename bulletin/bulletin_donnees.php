<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2008
 */

// Données relatives à l'établissement
	$gepiSchoolName=getSettingValue("gepiSchoolName");
	$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1");
	$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2");
	$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode");
	$gepiSchoolCity=getSettingValue("gepiSchoolCity");
	$RneEtablissement=getSettingValue("gepiSchoolRne");

// Données relatives au temps
	$gepiYear = getSettingValue('gepiYear');

// Données relatives à l'en-tête
	// le genre de la période (trimestre, période, semestre,...)
	if(getSettingValue("genre_periode")){
		$genre_periode=getSettingValue("genre_periode");
	}
	else{
		$genre_periode="M";
	}
	// Faire apparaitre le nom de l'établissement

	// Faire apparaitre le tel, fax.
	$gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
	$gepiSchoolTel = getSettingValue("gepiSchoolTel");
	$gepiSchoolFax = getSettingValue("gepiSchoolFax");

	/*
	if(!getSettingValue("bull_affiche_tel")){
		$bull_affiche_tel="n";
	}else{
		$bull_affiche_tel=getSettingValue("bull_affiche_tel");
	}
	if(!getSettingValue("bull_affiche_fax")){
		$bull_affiche_fax = "n";
	}else{
		$bull_affiche_fax = getSettingValue("bull_affiche_fax");
	}
	if($bull_affiche_fax=="y"){

	}
	if($bull_affiche_tel=="y"){
		$gepiSchoolTel = getSettingValue("gepiSchoolTel");
	}
	*/
?>