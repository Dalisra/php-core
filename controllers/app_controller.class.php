<?php

class APP_Controller {

    private $log;

    function App_Controller() {
        $this->log = Logger::getLogger("com.dalisra.controller");
    }

    function process(){
        $this->displayPageNotFoundError();
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

    /*
     * List of attributes:
     * table
     * url
     * name
     * dir
     * fields = array(name=>type)
     */
    function scaffoldModel($model){
        APP::$smarty->assign('model', $model);

        if(isset(APP::$request->path_arr[2])){
            if(isset($_REQUEST['id'])){
                $params = array();
                $params['from'] = $model['table'];
                $params['where'] = array("id"=>$_REQUEST['id']);
                $item = APP::$db->getData($params);
                if(isset($item) && isset($item[0])){
                    $item = $item[0];
                }
            }

            if(APP::$request->path_arr[2] == "edit"){

                if(isset($item) && isset($item['id'])){
                    foreach ($model['fields'] as $key => $val) {
                        //Getting subresources.
                        if ($val == "multiimage") {
                            if(!isset($item[$key])) $item[$key] = array();
                            $item[$key]['entity'] = $this->getUploadEntity();
                            $item[$key]['entity']['dir'] = $model['dir'];

                            $paramsInternal = array();
                            $paramsInternal['from'] = "uploads";
                            $paramsInternal['where'] = array("itemid"=>$item['id'], "tablename"=>$model['table'], "dir"=>$model['dir'], "field"=>$key);
                            $paramsInternal['orderby'] = $item[$key]['entity']['orderby'];
                            $paramsInternal['limit'] = 100;
                            $item[$key]['images'] = APP::$db->getData($paramsInternal);
                        }
                    }
                }

                if(isset($item)) APP::$smarty->assign('item', $item);
                if(isset($_REQUEST['raw']) && $_REQUEST['raw'] == "true"){
                    APP::$smarty->display('admin/editEntityRaw.tpl');
                }else{
                    APP::$smarty->display('admin/editEntity.tpl');
                }
                return;
            }elseif(APP::$request->path_arr[2] == "delete" && isset($item)){

                //TODO: delete all images.

                APP::$db->deleteDataById($model['table'], $_REQUEST['id']);
                foreach($model['fields'] as $key => $val){
                    if($val == "image"){
                        $this->deleteFile($item[$key]);
                    }
                }
                APP::$request->setMessage("Successfully deleted");
                APP::$request->jump($model['url']);
            }elseif(APP::$request->path_arr[2] == "save"){
                $this->log->debug("Trying to save item.");
                if(!isset($item)) $item = array();
                foreach ($model['fields'] as $key => $val) {
                    //WE are dealing with image. Lets download it.
                    if ($val == "image") {
                        $dir = 'images' . DS . $model['dir'] . DS;
                        if($model['uploads'] == true && $model['dir'] != "uploads"){
                            $dir = $dir . "uploads" . DS;
                        }
                        $path = $this->handleFile($dir, $key);
                        if(isset($path) && $path != false) { //if downloaded the file.
                            $prefixurl = 'images/' . $model['dir'] . '/';
                            if($model['uploads'] == true && $model['dir'] != "uploads"){
                                $prefixurl = $prefixurl . "uploads/";
                            }
                            $path = $prefixurl . $path;

                            //if the file path is different from before
                            if(isset($item[$key]) && $path != $item[$key]){
                                // delete the old file.
                                $this->log->debug("Calling delete file on: " . $item[$key]);
                                $this->deleteFile($item[$key]);
                            }
                            // set the new item path
                            $item[$key] = $path;
                        }
                    }
                    elseif ($val == "multiimage") {
                        //ignore
                    }
                    else {
                        $item[$key] = $_POST[$key];
                    }
                }
                if(isset($_POST['id'])){
                    APP::$db->updateDataById($model['table'], $item, $_POST['id']);
                    $item['id'] = $_POST['id'];
                }else{
                    $item['id'] = APP::$db->insertData($model['table'], $item);
                }
                if(isset($model['template'])){

                    $params = array();
                    $params['from'] = $model['table'];
                    $params['where'] = array('id'=>$item['id']);
                    $result = APP::$db->getData($params);
                    if($result) APP::$smarty->assign('item', $result[0]);
                    else APP::$smarty->assign('item', $item);
                    APP::$smarty->assign('parentElement', "dummy");
                    APP::$smarty->display($model['template']);
                    exit;
                }else{
                    APP::$request->setMessage("Lagret!");
                    APP::$request->jump($model['url']);
                }
            }
        }

        $params = array();
        $params['from'] = $model['table'];
        if(isset($model['filter']) && !empty($model['filter']) ){
            $params['where'] = $model['filter'];
        }
        //$params['where'] = array("public"=>1);
        $params['orderby'] = $model['orderby'];
        $params['limit'] = '1000';
        $items = APP::$db->getData($params);
        if($items){
            APP::$smarty->assign('items', $items);
        }
        if(isset($_REQUEST['display']) && $_REQUEST['display'] == "raw" ){
            APP::$smarty->display('admin/listEntitiesRaw.tpl');
        }
        APP::$smarty->display('admin/listEntities.tpl');
    }

    /*
     * Returns new image path if success.
     * Otherwise returts false
     */
    function handleFile($dir_path, $filename){
        $this->log->debug("Handling file:" . $filename);
        if(isset($_FILES[$filename]) && $_FILES[$filename]['name']){
            $this->log->debug("File upload Exists with name: " . $_FILES[$filename]['name']);
            if(!$_FILES[$filename]['error']){
                $this->log->debug("No errors, moving file to its new location");
                $newBaseName = date('m-d-Y-His') . basename($_FILES[$filename]["name"]);
                $newFilePath = APP::$conf['path']['full']. 'webroot' . DS . $dir_path . $newBaseName;
                if(move_uploaded_file($_FILES[$filename]['tmp_name'], $newFilePath)){
                    $this->log->debug("Moved file to new location: " . $newFilePath);
                    $this->log->debug("New Basename is: " . $newBaseName);
                    return $newBaseName;
                }
                $this->log->debug("Something went bad while moving file.");
            }else{
                APP::$request->setError($_FILES['photo']['error']);
                return false;
            }
        }
        return false;
    }

    function deleteFile($path){
        $this->log->debug("Deleting file: " . APP::$conf['path']['full'] . 'webroot' . DS . $path);
        if(unlink(APP::$conf['path']['full'] . 'webroot' . DS . $path)){
            $this->log->debug("Deleted!");
            return true;
        }
    }

    function processUploads(){

        $entity = $this->getUploadEntity();
        if(isset($_REQUEST['dir'])) $entity['filter']['dir'] = $_REQUEST['dir'];
        if(isset($_REQUEST['tablename'])) $entity['filter']['tablename'] = $_REQUEST['tablename'];
        if(isset($_REQUEST['field'])) $entity['filter']['field'] = $_REQUEST['field'];
        if(isset($_REQUEST['itemid'])) $entity['filter']['itemid'] = $_REQUEST['itemid'];
        if(isset($_REQUEST['dir'])) $entity['dir'] = $_REQUEST['dir'];
        else $entity['dir'] = "uploads";
        $this->handleEntity($entity);
    }

    function getUploadEntity(){
        $fields = array('image'=>'image', 'tablename'=>'text', 'title'=>'name', 'dir'=>'text', 'field'=>'text', 'itemid'=>'text', 'priority'=>'int');
        $names = array('image'=>'Bilde', 'tablename'=>'Tabell Navn', 'title'=>'Bilde Navn', 'dir'=>'Mappe', 'field'=>'Felt Navn', 'itemid'=>'Entitet Id', 'priority'=>'Prioritet');
        $entity = array();
        $entity['table'] = "uploads";
        $entity['url'] = "styrepanel/uploads";
        $entity['name'] = "uploads";
        $entity['title'] = "Opplastninger";
        $entity['uploads'] = true;
        $entity['orderby'] = "priority";
        $entity['fields'] = $fields;
        $entity['names'] = $names;
        $entity['template'] = "admin/elements/upload.tpl";
        $entity['filter'] = array();

        return $entity;
    }

    /*
     * Check if logged in then return true, otherwise redirect user to login page and quit.
     */
    function checkIfLoggedIn(){
        if(APP::$auth->isLoggedIn){
            return true;
        }else{
            $this->log->debug("User is not logged inn.. redirecting..");
            APP::$smarty->display('admin/login.tpl');
            exit;
        }
    }
}