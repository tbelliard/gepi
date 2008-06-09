<?php

/****************************************************
 * @class : RSSFeedTools
 * @parent : Object
 * @abstract : yes
 * @aim : library of tools which allow to validate feed information
 * @author : Hugo 'Emacs' HAMON
 * @email : webmaster[at]apprendre-php[dot]com
 * @version : 1.0
 * @changelog : see the changelog file
 ***************************************************/
 abstract class RSSFeedTools
 {
	/****************************************************
	* @function : checkEmail
	* @aim : check an email format
	* @access : public
	* @static : yes
	* @param :  string $email
	* @return : string $email
	***************************************************/
	public static function checkEmail($email) 
	{	
		if(!preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-.]?[[:alnum:]])*\.([a-z]{2,4})$`',$email))
		{
			throw new RSSFeedException('Your email format seems to be false', __CLASS__, __METHOD__);
		}
		else
		{
			return $email;
		} 
	}
				
	/****************************************************
	* @function : checkUrl
	* @aim : check an url format
	* @access : public
	* @static : yes
	* @param :  string $url
	* @return : string $url
	***************************************************/
	public static function checkUrl($url)
	{
		if(!preg_match('`((?:https?|ftp)://\S+[[:alnum:]]/?)`si',$url))
		{
			throw new RSSFeedException('Your url format seems to be false', __CLASS__, __METHOD__);
		}
		else
		{
			return $url;
		}
	}
	
	/****************************************************
	* @function : prepareFeedDate
	* @aim : set the feed date format
	* @access : public
	* @static : yes
	* @param :  string $date
	* @return : boolean true / false
	***************************************************/
	public static function prepareFeedDate($date)
	{
		// Local data structures
		$dateParts = array();
		
	 // Check the date format
		if(preg_match('`^((\d{4})\-((0[1-9])|(1[0-2]))\-((0[1-9])|(1\d)|(2\d)|(3[0-1]))(((([[:space:]]?)(([0-1][0-9])|([2][0-3]))(:[0-5][0-9]))((:[0-5][0-9])?))?))$`',$date))
		{
		    // Explode the format
		    $dateParts = date_parse($date);
		    
		    // Check we have got hours, minutes and seconds information
		    if(!empty($dateParts['hour']) 
   				&& !empty($dateParts['minute']) 
   				&& !empty($dateParts['second']))
		    {
  				return strftime('%a, %d %b %Y %H:%M:%S %z', mktime(intval($dateParts['hour']), intval($dateParts['minute']), intval($dateParts['second']), intval($dateParts['month']), intval($dateParts['day']), intval($dateParts['year'])));
		    }
		    else
		    {
				return strftime('%a, %d %b %Y 00:00:00 %z', mktime(intval($dateParts['hour']), intval($dateParts['minute']), intval($dateParts['second']), intval($dateParts['month']), intval($dateParts['day']), intval($dateParts['year'])));
		    }
		}
		else
		{
			  throw new RSSFeedException('The specified date format is not valid', __CLASS__, __METHOD__);
		}
	}
	
} // END CLASS

?>