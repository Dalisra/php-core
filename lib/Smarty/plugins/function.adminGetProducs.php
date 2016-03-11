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
function smarty_function_adminGetProducts(array $args, Smarty_Internal_Template $smarty){

	$log = Logger::getLogger("com.dalisra.smartyfunction.adminGetProducts");

    $log->debug("Preparing Query");
    $params = array();
    $params['select'] = 'p.*, ps.priority';
    $params['from'] = 'products p';
    $params['join'] = 'productstructures ps';
    $params['on'] = array('p.id'=>'ps.product_id');


    if(!empty($args['cat'])){ // is there filter on category?
        $params['where'] = array();
        $params['where']['ps.category_id'] = (int)$args['cat']['id']; //We filter by category.
    }
    $params['orderby'] = "priority";
    $list = APP::$db->getData($params);
    if(!$list){
        $log->debug("Got error executing query: " . APP::$db->db_error);
    }
    $smarty->assign("list", $list);

}