<?php

if(date('m') > 7) {
   $annee = date('Y') - 1 ;
} else {
   $annee = date('Y') - 2 ;
}

$annee2 = $annee + 1 ;
define ('trimestre1' , $annee.'-12-01');
define ('trimestre2' , $annee2.'-03-01');
define ('trimestre3' , $annee2.'-07-01');

$date = new DateTime();

/* jours        1ères dates → dates en erreur en 2013 → dates en 2013 */

/* 2005-01-01 */
$date->setISODate($annee - 5, 52, 7);
define ('FIN_ANNEE_5a' , $date->format('Y-m-d'));
/* samedi      2005-10-01 */
$date->setISODate($annee - 5, 39, 6);
define ('SAMEDI_5a_s39j6' , $date->format('Y-m-d'));
/*jeudi        2011-12-01 */
$date->setISODate($annee - 1, 48, 4);
define ('JEUDI_1a_s48j4' , $date->format('Y-m-d'));
/*jeudi        2012-08-30 */
$date->setISODate($annee, 35, 4);
define ('JEUDI_1a_s35j4' , $date->format('Y-m-d'));
/* vendredi    2012-08-31 */
$date->setISODate($annee, 35, 5);
define ('VENDREDI_s35j5' , $date->format('Y-m-d'));
/* samedi      2012-09-01 */
$date->setISODate($annee, 35, 6);
define ('SAMEDI_s35j6' , $date->format('Y-m-d'));
/* dimanche     2012-09-02 */
$date->setISODate($annee, 35, 7);
define ('DIMANCHEs35j7' , $date->format('Y-m-d'));
/* mardi       2012-09-18 */
$date->setISODate($annee, 38, 2);
define ('MARDI_s38j2' , $date->format('Y-m-d'));
/* mercredi    2012-09-19 */
$date->setISODate($annee, 38, 3);
define ('MERCREDI_s38j3' , $date->format('Y-m-d'));
/* jeudi       2012-09-20 */
$date->setISODate($annee, 38, 4);
define ('JEUDI_s38j4' , $date->format('Y-m-d'));

/* mardi       2012-10-02 */
$date->setISODate($annee, 40, 2);
define ('MARDI_s40j2' , $date->format('Y-m-d'));
/* jeudi       2012-10-04 */ 
$date->setISODate($annee, 40, 4);
define ('JEUDI_s40j4' , $date->format('Y-m-d'));
/* vendredi    2010-10-01 → 2013-10-04 → 2012-10-05 */ 
$date->setISODate($annee, 40, 5);
define ('VENDREDI_s40j5' , $date->format('Y-m-d'));
/* samedi      2010-10-02 → 2013-10-05 → 2012-10-06 */
$date->setISODate($annee, 40, 6);
define ('SAMEDI_s40j6' , $date->format('Y-m-d'));
/* dimanche    2010-10-03 → 2013-10-06 → 2012-10-07 */
$date->setISODate($annee, 40, 7);
define ('DIMANCHE_s40j7' , $date->format('Y-m-d'));
/* lundi       2010-10-04 → 2013-10-07 → 2012-10-08 */
$date->setISODate($annee, 41, 1);
define ('LUNDI_s41j1' , $date->format('Y-m-d'));
/* mardi       2010-10-05 → 2013-10-08 → 2012-10-09 */
$date->setISODate($annee, 41, 2);
define ('MARDI_s41j2' , $date->format('Y-m-d'));
/* mercredi    2010-10-06 → 2013-10-09 → 2012-10-10 */
$date->setISODate($annee, 41, 3);
define ('MERCREDI_s41j3' , $date->format('Y-m-d'));
/* jeudi       2010-10-07 → 2013-10-10 → 2012-10-11 */
$date->setISODate($annee, 41, 4);
define ('JEUDI_s41j4' , $date->format('Y-m-d'));
/* vendredi    2010-10-08 → 2013-10-11 → 2012-10-12 */
$date->setISODate($annee, 41, 5);
define ('VENDREDI_s41j5' , $date->format('Y-m-d'));
/* samedi      2010-10-09 → 2013-10-12 → 2012-10-13 */
$date->setISODate($annee, 41, 6);
define ('SAMEDI_s41j6' , $date->format('Y-m-d'));
/* dimanche    2010-10-10 → 2013-10-13 → 2012-10-14 */
$date->setISODate($annee, 41, 7);
define ('DIMANCHE_s41j7' , $date->format('Y-m-d'));
/* lundi       2010-10-11 → 2013-10-14 → 2012-10-15 */
$date->setISODate($annee, 42, 1);
define ('LUNDI_s42j1' , $date->format('Y-m-d'));
/* mardi       2010-10-12 → 2013-10-15 → 2012-10-16 */
$date->setISODate($annee, 42, 2);
define ('MARDI_s42j2' , $date->format('Y-m-d'));
/* mercredi    2010-10-13 → 2013-10-16 → 2012-10-17 */
$date->setISODate($annee, 42, 3);
define ('MERCREDI_s42j3' , $date->format('Y-m-d'));
/* jeudi       2010-10-14 → 2013-10-17 → 2012-10-18 */
$date->setISODate($annee, 42, 4);
define ('JEUDI_s42j4' , $date->format('Y-m-d'));
/* vendredi    2010-10-15 → 2013-10-18 → 2012-10-19 */
$date->setISODate($annee, 42, 5);
define ('VENDREDI_s42j5' , $date->format('Y-m-d'));
/* samedi      2010-10-16 → 2013-10-19 → 2012-10-20 */
$date->setISODate($annee, 42, 6);
define ('SAMEDI_s42j6' , $date->format('Y-m-d'));
/* dimanche    2010-10-17 → 2013-10-20 → 2012-10-21 */
$date->setISODate($annee, 42, 7);
define ('DIMANCHE_s42j7' , $date->format('Y-m-d'));
/* lundi       2010-10-18 → 2013-10-21 → 2012-10-22 */
$date->setISODate($annee, 43, 1);
define ('LUNDI_s43j1' , $date->format('Y-m-d'));
/* mardi       2010-10-19 → 2013-10-22 → 2012-10-23 */
$date->setISODate($annee, 43, 2);
define ('MARDI_s43j2' , $date->format('Y-m-d'));
/* mercredi    2010-10-20 → 2013-10-23 → 2012-10-24 */
$date->setISODate($annee, 43, 3);
define ('MERCREDI_s43j3' , $date->format('Y-m-d'));
/* jeudi       2012-10-25 */
$date->setISODate($annee, 43, 4);
define ('JEUDI_s43j4' , $date->format('Y-m-d'));
/* dimanche    2010-10-24 → 2013-10-27 → 2012-10-28 */
$date->setISODate($annee, 43, 7);
define ('DIMANCHE_s43j7' , $date->format('Y-m-d'));
/* jeudi       2010-10-28 → 2013-10-31 → 2012-11-01 */
$date->setISODate($annee, 44, 4);
define ('JEUDI_s44j4' , $date->format('Y-m-d'));
/* samedi      2010-10-30 → 2013-11-03 → 2012-11-04 */
$date->setISODate($annee, 44, 6);
define ('SAMEDI_s44j6' , $date->format('Y-m-d'));
/* dimanche    2010-10-31 → 2013-11-04 → 2012-11-05 */
$date->setISODate($annee, 44, 7);
define ('DIMANCHE_s44j7' , $date->format('Y-m-d'));
/* mardi       2010-11-02 → 2013-11-05 → 2012-11-06 */
$date->setISODate($annee, 45, 2);
define ('MARDI_s45j2' , $date->format('Y-m-d'));
/* vendredi    2012-11-09 */
$date->setISODate($annee, 45, 5);
define ('VENDREDI_s45j5' , $date->format('Y-m-d'));
/* dimanche    2010-11-07 → 2013-11-10 → 2012-11-11 */
$date->setISODate($annee, 45, 7);
define ('DIMANCHE_s45j7' , $date->format('Y-m-d'));
/* samedi      2012-12-01 */
$date->setISODate($annee, 48, 6);
define ('SAMEDI_s48j6' , $date->format('Y-m-d'));
/* jeudi       2012-12-06 */
$date->setISODate($annee, 49, 4);
define ('JEUDI_s49j4' , $date->format('Y-m-d'));
/* dimanche    2010-12-05 → 2013-12-08 → 2012-12-09 */
$date->setISODate($annee, 49, 7);
define ('DIMANCHE_s49j7' , $date->format('Y-m-d'));
/* mardi       2012-12-11 */
$date->setISODate($annee, 50, 2);
define ('MARDI_s50j2' , $date->format('Y-m-d'));
/* lundi       2010-12-20 → 2013-12-23 → 2012-12-24 */
$date->setISODate($annee, 52, 1);
define ('LUNDI_s52j1' , $date->format('Y-m-d'));
/* samedi      2013-03-02 */
$date->setISODate($annee + 1, 9, 6);
define ('SAMEDI_a1_s9j6' , $date->format('Y-m-d'));
/* dimanche    2013-03-31 */
$date->setISODate($annee + 1, 14, 7);
define ('DIMANCHE_ETE' , $date->format('Y-m-d'));
/* samedi      2013-05-11 */
$date->setISODate($annee + 1, 19, 6);
define ('SAMEDI_a1_s19j6' , $date->format('Y-m-d'));
/* lundi       2011-05-30 → 2014-05-26 → 2013-05-27 */
$date->setISODate($annee + 1, 22, 1);
define ('LUNDI_a1_s22j1' , $date->format('Y-m-d'));
define ('LUNDI_time_a1_s22j1' , $date->format('d-m-Y'));
/* mardi       2011-05-31 → 2014-05-27 → 2013-05-28 */
$date->setISODate($annee + 1, 22, 2);
define ('MARDI_a1_s22j2' , $date->format('Y-m-d'));
/* mercredi    2011-06-01 → 2014-05-28 → 2013-05-29 */
$date->setISODate($annee + 1, 22, 3);
define ('MERCREDI_a1_s22j3' , $date->format('Y-m-d'));
/* jeudi       2011-06-02 → 2014-05-29 → 2013-05-30 */
$date->setISODate($annee + 1, 22, 4);
define ('JEUDI_a1_s22j4' , $date->format('Y-m-d'));
/* vendredi    2011-06-03 → 2014-05-30 → 2013-05-31 */
$date->setISODate($annee + 1, 22, 5);
define ('VENDREDI_a1_s22j5' , $date->format('Y-m-d'));
/* lundi       2011-06-06 → 2014-06-02 → 2013-06-03 */
$date->setISODate($annee + 1, 23, 1);
define ('LUNDI_a1_s23j1' , $date->format('Y-m-d'));
/* mardi       2011-06-07 → 2014-06-03 → 2013-06-04 */
$date->setISODate($annee + 1, 23, 2);
define ('MARDI_a1_s23j2' , $date->format('Y-m-d'));
/* mercredi    2011-06-08 → 2014-06-04 → 2013-06-05 */
$date->setISODate($annee + 1, 23, 3);
define ('MERCREDI_a1_s23j3' , $date->format('Y-m-d'));
/* jeudi       2011-06-09 → 2014-06-05 → 2013-06-06 */
$date->setISODate($annee + 1, 23, 4);
define ('JEUDI_a1_s23j4' , $date->format('Y-m-d'));
/* vendredi    2011-06-10 → 2014-06-06 → 2013-06-07 */
$date->setISODate($annee + 1, 23, 5);
define ('VENDREDI_a1_s23j5' , $date->format('Y-m-d'));
/* samedi      2011-06-11 → 2014-06-07 → 2013-06-08 */
$date->setISODate($annee + 1, 23, 6);
define ('SAMEDI_a1_s23j6' , $date->format('Y-m-d'));
/* dimanche    2011-06-12 → 2014-06-08 → 2013-06-09 */
$date->setISODate($annee + 1, 23, 7);
define ('DIMANCHE_a1_s23j7' , $date->format('Y-m-d'));
/* lundi       2011-06-13 → 2014-06-09 → 2013-06-10 */
$date->setISODate($annee + 1, 24, 1);
define ('LUNDIa1_s24j1' , $date->format('Y-m-d'));
/* lundi       2013-07-01 */
$date->setISODate($annee + 1, 27, 1);
define ('LUNDI_a1_s27j1' , $date->format('Y-m-d'));

?>
