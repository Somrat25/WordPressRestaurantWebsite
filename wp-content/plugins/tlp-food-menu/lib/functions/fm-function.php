<?php
if(!function_exists('FM_get_template')){

	function FM_get_template($fileName){
		$file = TLPFoodMenu()->getTemplatesPath() . $fileName;
		if(file_exists($file)){
			return include($file);
		}

		return false;
	}

}
