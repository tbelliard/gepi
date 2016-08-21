<?php

/****************************************************
 * @class : RSSFeedException
 * @parent : Exception
 * @abstract : no
 * @aim : manage exceptions
 * @author : Hugo 'Emacs' HAMON
 * @email : webmaster[at]apprendre-php[dot]com
 * @version : 1.0
 * @changelog : see the changelog file
 ***************************************************/
 class RSSFeedException extends Exception
 {
 	// Attributes
	private $_method = '';
	private $_class = '';

	// Constructor
	
	/****************************************************
	* @function : __construct
	* @aim : create the new instance
	* @access : public
	* @static : no
	* @param :  string $message
	* @param : string $class
	* @param : string $method
	* @return : void
	***************************************************/
	public function __construct($message, $class, $method) 
	{
		$this->_class = $class;
		$this->_method = $method;
		parent::__construct($message);
	}
	
	// __toString
	
	/****************************************************
	* @function : __toString
	* @aim : display the exception
	* @access : public
	* @static : no
	* @param :  void
	* @return : void
	***************************************************/
	public function __toString()
	{
		echo $this->getErrorMessage();	
	}
	
	// Get methods
	
	/****************************************************
	* @function : getErrorMessage
	* @aim : display the exception
	* @access : public
	* @static : no
	* @param :  void
	* @return : string $return
	***************************************************/
	public function getErrorMessage() 
	{
	
		$return = '<pre class="error">'."\n";
		$return.= '<p>An exception has been raised :</p>'."\n";
		$return.= '<p>'."\n";
		$return.= '<strong>File :</strong> '. $this->getFile() .'<br/>'."\n";
		$return.= '<strong>Line :</strong> '. $this->getLine() .'<br/>'."\n";
		$return.= '<strong>Class :</strong> '. $this->_class .'<br/>'."\n";
		$return.= '<strong>Method :</strong> '. $this->_method .'<br/>'."\n";
		$return.= '<strong>Time :</strong> '. date('Y-m-d H:i:s') .'<br/>'."\n";
		$return.= '<strong>Message :</strong> '. $this->getMessage() ."\n"."\n";
		$return.= '</p>'."\n";
		$return.= '</pre>'."\n";
		
		return $return;
	}
	
 }
 
 ?>