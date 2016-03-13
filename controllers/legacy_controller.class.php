<?php

class APP_Controller {

    private $log;
    var $table;
    var $item;
    var $fields = array("name, active");
	
    function App_Controller() {
            $this->log = Logger::getLogger("com.dalisra.controller");
    }
	
    function process(){
        if(isset($_REQUEST['id']) && isValidInteger($_REQUEST['id'])){
            $this->log->debug("Request id was valid integer, getting item.");
            $item = $this->getItem($_REQUEST['id']);
            if($item){
                $this->log->debug("Found item, now displaying it..");
                $this->show($item[0]);
            }else{
                $this->log->error("Got id: " . $_REQUEST['id'] . " but no such item found in database");
                $this->displayPageNotFoundError();
            }
        }else{
            $this->log->debug("Request id was NOT valid integer.");
            $this->displayPageNotFoundError();
        }
    }

    function adminProcess(){
        if(isset(APP::$request->path_arr[1])){
            $this->log->debug("adminProcess - path_arr[1] is set:" . APP::$request->path_arr[1]);
            //check for reserved words
            if(APP::$request->path_arr[1] == "edit"){
                $this->adminEditItem();
            }elseif(APP::$request->path_arr[1] == "new"){
                //display form for new item creation
                $this->adminNewItem();
            }elseif(APP::$request->path_arr[1] == "ajax"){
                $this->adminAjax();
            }else {
                $this->displayPageNotFoundError();
            }
        }else{
            if(isset($_REQUEST['act'])){ //check for actions
                $success = $this->executeAct();
                if(!$success){
                    //TODO: try some custom acts.
                }
                //Do a jump after act.
                APP::$request->jump(APP::$request->url);
            }else{
                //we display all the products
                $list = $this->adminGetList();
                $this->adminShowList($list);
            }
        }
    }

    /**
     * Returns true if act was found.
     */
    function executeAct(){
        if($_REQUEST['act'] == "save"){
            //we saving or inserting item
            $this->adminSaveItem();
            return true;
        }
        if($_REQUEST['act'] == "delete"){
            //we deleting item
            $this->adminDeleteItem();
            return true;
        }
        if($_REQUEST['act'] == "activate"){
            //we activating item
            $this->adminActivateItem();
            return true;
        }
        if($_REQUEST['act'] == "deactivate"){
            //we deactivating item
            $this->adminDeactivateItem();
            return true;
        }
        return false;
    }

    function adminEditItem(){
        if(!isset($_REQUEST['id'])){
            $this->displayPageNotFoundError();
        }else{
            $id = (int)$_REQUEST['id'];
            if($id && $id > 0){
                $item = $this->adminGetItem($id);
                if($item) {
                    $this->adminShow($item[0]);
                }
                else{
                    $this->log->error("Got id: " . $id . " but no such item in database");
                    $this->displayPageNotFoundError();
                }
            }else{
                $this->displayPageNotFoundError();
            }
        }
    }

    function adminNewItem(){
        $this->adminShow();
    }

    function adminActivateItem(){
        if(isset($_REQUEST['id']) && $_REQUEST['id'] == (int)$_REQUEST['id']){
            $item = array();
            $item['active'] = "true";
            $this->log->debug("Activating item with id:" . $_REQUEST['id']);
            $result = APP::$db->updateDataById($this->table, $item, $_REQUEST['id']);
            $this->log->debug("Activating completed with result: " . $result);
        } else {
            APP::$log->error("Activation was canceled, no id.");
        }
    }

    function adminDeactivateItem(){
        if(isset($_REQUEST['id']) && $_REQUEST['id'] == (int)$_REQUEST['id']){
            $item = array();
            $item['active'] = "false";
            $this->log->debug("Deactivating item with id:" . $_REQUEST['id']);
            $result= APP::$db->updateDataById($this->table, $item, $_REQUEST['id']);
            $this->log->debug("Deactivating completed with result: " . $result);
        } else {
            APP::$log->error("Deactivating was canceled, no id.");
        }
    }

    function adminDeleteItem(){
        if(isset($_REQUEST['id']) && $_REQUEST['id'] == (int)$_REQUEST['id']){
            $this->log->debug("Deleting item with id:" . $_REQUEST['id']);
            $result = APP::$db->deleteDataById($this->table, $_REQUEST['id']);
            $this->log->debug("Deleting completed with result: " . $result);
        } else {
            $this->log->error("Deleting was canceled, no id.");
        }
    }

    function adminSaveItem(){
        $item = array();
        foreach($this->fields as $field){
            if(isset($_REQUEST[$field])){
                $item[$field] = $_REQUEST[$field];
            }
        }
        if(isset($_REQUEST['id'])){
            $id = (int)$_REQUEST['id'];
            $this->log->debug("Updating item with id:" . $id);
            $result = APP::$db->updateDataById($this->table, $item, $id);
            $this->log->debug("Updating completed with result: " . $result);
        }else{
            $this->log->debug("Inserting new item");
            $id = APP::$db->insertData($this->table, $item);
            $this->log->debug("Insert completed, id returned: " . $id);

            $this->adminDoAfterInsert($id);
        }
    }

    function adminDoAfterInsert($id){
        $this->log->debug("Id after insert is: " . $id);
    }

    function adminShowList($list = "isEmpty"){
        if($list == "isEmpty" || $list == false || sizeof($list) == 0){
            APP::$smarty->assign("isEmpty", true);
        }else{
            APP::$smarty->assign("isEmpty", false);
            APP::$smarty->assign("list", $list);
        }
        APP::$smarty->display("pages/$this->item.tpl");
    }

    function adminShow($item = "isNew"){
        if($item == "isNew"){
            APP::$smarty->assign("isNew", true);
        }else{
            APP::$smarty->assign("isNew", false);
            APP::$smarty->assign("item", $item);
        }
        APP::$smarty->display("pages/" . $this->item . "_form.tpl");
    }

    /**
     * You should override this function in your controller.
     * If you want any filtering.
     */
    function adminGetList(){
        return $this->getAll();
    }

    function getAll(){
        $params = array();
        $params['from'] = $this->table;
        $params['limit'] = 100;

        if(isset($_REQUEST['offset']) && $_REQUEST['offset'] == (int)$_REQUEST['offset']){
            $params['offset'] = $_REQUEST['offset'];
        }
        $params['orderby'] = "priority";
        return APP::$db->getData($params);
    }
	
	function getItem($id){
		$params = array();
		$params['from'] = $this->table;
		$params['where'] = array('id'=>$id, 'active'=>"true");
		return APP::$db->getData($params);
	}

    function adminGetItem($id){
        $params = array();
        $params['from'] = $this->table;
        $params['where'] = array('id'=>$id);
        return APP::$db->getData($params);
    }

	function displayPageNotFoundError(){
    	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
    	APP::$smarty->assign("error_msg", "Stien finnes ikke");
	    APP::$smarty->assign("error_nr", "404");
    	$protocol = "HTTP/1.0";
    	if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
        	$protocol = "HTTP/1.1";
    	}
    	header( "$protocol 404 Page Not Found", true, 404 );
    	$this->log->error("Displaying 404 error. Url was not found, request: " . print_r($_REQUEST, true));
	    APP::$smarty->display('error_pages/404.tpl');	
	}

    function displayPageNotImplementedError(){
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
        APP::$smarty->assign("error_msg", "Not implemented");
        APP::$smarty->assign("error_nr", "501");
        $protocol = "HTTP/1.0";
        if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
            $protocol = "HTTP/1.1";
        }
        header( "$protocol 501 Not implemented", true, 404 );
        $this->log->error("Displaying 501 error. Missing Smarty template, request: " . print_r($_REQUEST, true));
        APP::$smarty->display('error_pages/501.tpl');
    }
}