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
function smarty_function_getProductList(array $args, Smarty_Internal_Template $smarty){

	$log = Logger::getLogger("com.dalisra.smartyfunction.getProductList");

    $log->debug("Preparing Query");
    $params = array();
    $params['select'] = 'p.*, ps.priority';
    $params['from'] = 'products p';
    $params['join'] = 'productstructures ps';
    $params['on'] = array('p.id'=>'ps.product_id');

    $params['where'] = array();
    $params['where']['p.active'] = "true"; //We get only active products.
    if(!empty($args['cat'])){ // is there filter on category?
        $params['where']['ps.category_id'] = (int)$args['cat']['id']; //We filter by category.
    }
    if(!empty($args['type'])){ // is there filter on type?
        $params['where']['ps.type'] = $args['type']; // We filter by type.
    }

    $params['orderby'] = 'ps.priority';
    $productList = APP::$db->getData($params);
    if(!$productList){
        $log->debug("Got error executing query: " . APP::$db->db_error);
    }
    $smarty->assign("productList", $productList);

}