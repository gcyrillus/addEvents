<?php  if(!defined('PLX_ROOT')) exit; 

	if($module==false || $module =='list'){	
		include('modules/list.php');
		} 
       else  { 
	   try{
		   include('modules/'.$module.'.php');
		   }
	catch (Exeption $er) {
		// silent ! there is no module of that kind
		echo 'module absent';
		}
	} ?>