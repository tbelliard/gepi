<?php

/*
$Id: mincals.inc 3875 2009-12-05 17:29:29Z crob $
*/



function minicals($year, $month, $day, $id_groupe, $dmy) {

// PHP Calendar Class
//
// Copyright David Wilkinson 2000. All Rights reserved.
//
// This software may be used, modified and distributed freely
// providing this copyright notice remains intact at the head
// of the file.
//
// This software is freeware. The author accepts no liability for
// any loss or damages whatsoever incurred directly or indirectly
// from the use of this script.
//
// URL:   http://www.cascade.org.uk/software/php/calendar/
// Email: davidw@cascade.org.uk

class Calendar
{
    var $month;
    var $year;
    var $day;
    var $h;
    var $id_groupe;
    var $dmy;

    function Calendar($day, $month, $year, $h, $id_groupe, $dmy)
    {
        $this->day   = $day;
        $this->month = $month;
        $this->year  = $year;
        $this->h     = $h;
        $this->id_groupe  = $id_groupe;
        $this->dmy   = $dmy;
    }

    function getRneMultisite(){
    	// Pour le multisite, on doit récupérer le RNE de l'établissement
		$rne = isset($_GET['rne']) ? $_GET['rne'] : (isset($_POST['rne']) ? $_POST['rne'] : 'aucun');
		$aff_get_rne = NULL;
		if ($rne != 'aucun') {
			return '&amp;rne=' . $rne;
		}else{
			return '';
		}
    }

   function getCalendarLink($month, $year, $id_groupe)
   {
        global $id_classe;
        global $selected_eleve_login;
        $day=1;
        return $this->dmy."year=$year&amp;month=$month&amp;day=$day&amp;id_groupe=$id_groupe&amp;id_classe=$id_classe&amp;login_eleve=$selected_eleve_login" . $this->getRneMultisite();
    }
	// correction w3c : ajout de \" pour encadrer le lien
   function getDateLink($day, $month, $year) {
      global $id_classe;
      global $selected_eleve_login;
      return "<a title=\"Voir le cahier de textes pour cette journée\" href=\"".$this->dmy."year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=".$this->id_groupe . $this->getRneMultisite() . "\"";
   }


    function getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12)
        {
            return 0;
        }

        $days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

        $d = $days[$month - 1];

        if ($month == 2)
        {
            // Check for leap year
            // Forget the 4000 rule, I doubt I'll be around then...

            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $d = 29;
                    }
                }
                else
                {
                    $d = 29;
                }
            }
        }

        return $d;
    }

    function getFirstDays()
    {
      global $weekstarts;

      $basetime = mktime(12,0,0,6,11+$weekstarts,2000);
      for ($i = 0, $s = ""; $i < 7; $i++)
      {
         $show = $basetime + ($i * 24 * 60 * 60);
         $fl = strftime('%a',$show);
         $s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">$fl</td>\n";
      }
      return $s;
    }


    function getHTML()

    {

        global $weekstarts;
        global $gepiPath;

        if (!isset($weekstarts)) $weekstarts = 0;

        $s = "";

        $daysInMonth = $this->getDaysInMonth($this->month, $this->year);
        $date = mktime(12, 0, 0, $this->month, 1, $this->year);

        $first = (strftime("%w",$date) + 7 - $weekstarts) % 7;
        $monthName = strftime("%B",$date);

        $prevMonth = $this->getCalendarLink($this->month - 1 >   0 ? $this->month - 1 : 12, $this->month - 1 >   0 ? $this->year : $this->year - 1, $this->id_groupe);
        $nextMonth = $this->getCalendarLink($this->month + 1 <= 12 ? $this->month + 1 :  1, $this->month + 1 <= 12 ? $this->year : $this->year + 1, $this->id_groupe);
	// correction w3c : \" pour encadrer la valeur de border, align, valign, colspan
        $s .= "<table class=\"calendar\" border=\"1\" summary=\"Tableau calendrier\">\n";
        $s .= "<tr><td class=\"calendarcol1\" align=\"right\" valign=\"top\"></td>\n";
        $s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . (($prevMonth == "") ? "&nbsp;" : "<a href=\"$prevMonth\"><img src='".$gepiPath."/images/icons/back.png' alt='Mois précédent' /></a>")  . "</td>\n";
        $s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\" colspan=\"5\">";

        $s .= "$monthName&nbsp;$this->year";

    $s .= "</td>\n";
        $s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">" . (($nextMonth == "") ? "&nbsp;" : "<a href=\"$nextMonth\"><img src='".$gepiPath."/images/icons/forward.png' alt='Mois suivant' /></a>")  . "</td>\n";
        $s .= "</tr>\n";

        $s .= "<tr><td  class=\"calendarcol1\" align=\"right\" valign=\"top\"></td>\n";
        $s .= $this->getFirstDays();
        $s .= "</tr>\n";

        $d = 1 - $first;
        $temp = 1;
    $numweek=1;
        while ($d <= $daysInMonth)
        {
        $s .= "<tr><td class=\"calendarcol1\" align=\"right\" valign=\"top\">";
            $s .= "</td>\n";
            for ($i = 0; $i < 7; $i++)
            {
                $s .= "<td class=\"calendar\" align=\"right\" valign=\"top\">";
                if ($d > 0 && $d <= $daysInMonth)
                {
                    $link = $this->getDateLink($d, $this->month, $this->year);
                    if ($link == "")
                        $s .= $d;
                    elseif (($d == $this->day) and ($this->h))
                        $s .= $link."><span class=\"cal_current_day\">$d</span></a>";
                    else
                        $s .= $link.">$d</a>";
                }
                else
                {
                    $s .= "&nbsp;";
                }
                $s .= "</td>\n";
                $d++;
            }
            $s .= "</tr>\n";
        }

        $s .= "</table>\n";

        return $s;
    }
}

$thismonth = mktime(0, 0, 0, $month,   $day, $year);
$cal = new Calendar(date("d",$thismonth), date("m",$thismonth), date("Y",$thismonth), 1, $id_groupe, $dmy);
echo $cal->getHTML();

}
?>
