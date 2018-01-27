<?php

/**
 * Versioning now done via git - when using a versioned branch.
 */
//Maintained for application reference
if( !defined("IDEA_CURRENT_VERSION") ) define("IDEA_CURRENT_VERSION",5);
if( !defined("IDEA_VERSION") ){	define("IDEA_VERSION",IDEA_CURRENT_VERSION);	}

spl_autoload_register(function($class_name){ @include_once dirname(__FILE__).'/src/'.$class_name.'.class'.INC_END; });
