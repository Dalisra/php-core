<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.getProductList.php
 * Type:     function
 * Name:     getProductList
 * Purpose:  gets list of products base on category id and type
 * -------------------------------------------------------------
 */
function smarty_function_adminGetCategories(array $args, Smarty_Internal_Template $smarty){

	$log = Logger::getLogger("com.dalisra.smartyfunction.adminGetCategories");

    $log->debug("Preparing Query");
    $params = array();
    $params['from'] = 'categories';
    $params['orderby'] = "priority";
    $list = APP::$db->getData($params);
    if(!$list){
        $log->debug("Got error executing query: " . APP::$db->db_error);
    }

    $smarty->assign("list", $list);

}