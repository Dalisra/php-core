<?php

/**
 * Created by PhpStorm.
 * User: vytautas
 * Date: 01.05.2016
 * Time: 19:48
 */
class Admin_Controller extends APP_Controller
{
    protected $log;

    public function __construct() {
        $this->log = Logger::getLogger("com.dalisra.controllers.admin");
    }

    function process($urlArray = []) {
        $this->log->debug("Starting processing");
        if($this->checkIfLoggedIn($urlArray)){
            //$menus = MenuService::getAdminMenuItems();
            //APP::$smarty->assign('menus', $menus);
            //APP::$smarty->assign('activeMenu', "Hjem");
            APP::$smarty->display('admin/pages/index.tpl');
        }
    }

    function processUsers($urlArray = []){

        //Define a page.
        $page = new APP_Page();
        $page->isConfig = false;
        $page->isList = true;
        $page->table = "users";
        $page->url = "users";
        $page->name = "users";
        $page->title = "Brukere";
        $page->dir = "users";

        //TODO: add image to user.
        //$page->listFields[] = APP_Field::generateImage("image", "Bilde");
        $page->listFields[] = APP_Field::generateName();
        $page->listFields[] = APP_Field::generateText("username", "Brukernavn");
        $passwordField = APP_Field::generateText("password", "Passord", false, true); $passwordField->type = APP_Field::$TYPE_PASSWORD;
        $page->listFields[] = $passwordField;
        $page->listFields[] = APP_Field::generateInt("logins", "Antall ganger pålogget");
        $page->listFields[] = APP_Field::generateInt("created", "Dato opprettet", true, false);
        $page->listFields[] = APP_Field::generateInt("updated", "Sist redigert", true, false);
        $page->listFields[] = APP_Field::generateBoolean("active", "Aktiv", true, true);

        $this->handlePage($urlArray, $page);
    }
    /*
    * Check if logged in then return true, otherwise redirect user to login page and quit.
    */
    function checkIfLoggedIn($urlArray = []){
        $this->log->debug("Authenticating..");
        if(APP::$auth->isLoggedIn){
            $this->log->debug(".. user is logged inn. Approved.");
            return true;
        }else{
            $this->log->debug("User is not logged inn.. redirecting..");
            //The jump will exit the processing of this request, so we do not need to return anything.
            $this->processLogin($urlArray);
            exit;
        }
    }

    function processLogin($urlArray = []){
        if(!APP::$auth->isLoggedIn){
            $this->log->debug("Somebody is trying to login, lets display login page.");
            APP::$smarty->display('admin/pages/login.tpl');
        }else{
            $this->process($urlArray);
        }
    }

    function processLogout($urlArray = []){
        if($this->checkIfLoggedIn($urlArray)) {
            $this->log->debug("Logging out!");
            APP::$auth->logout();
            APP::$request->jump("styrepanel");
        }
    }

    /**
     * Handles page rendering.
     * @param $urlArray array
     * @param $page APP_Page
     */
    function handlePage($urlArray, $page){
        if($this->checkIfLoggedIn()){
            APP::$smarty->assign("page", $page);
            if(isset($urlArray[0])){
                //some extra action has been sent for this page.
                $this->handleAction($urlArray, $page);
            }else{
                //We Display default view
                if($page->isConfig){
                    $this->handleConfig($urlArray, $page);
                }else{
                    $this->handleList($urlArray, $page);
                }
            }
        }
    }

    /**
     * Handles page rendering.
     * @param $urlArray array
     * @param $page APP_Page
     */
    function handleAction($urlArray, $page){
        $item = false;
        if(isset($_REQUEST['id'])){
            $item = APP::$db->getDataById($page->table, $_REQUEST['id']);
        }
        if($item){
            //TODO: get all subresources.
            APP::$smarty->assign('item', $item);
        }

        if($urlArray[0] == "save") {
            $this->log->debug("Handling action: save");
            if(!isset($item)) $item = array();

            foreach($page->listFields as $field){
                /* @var $field APP_Field */

                if(isset($_POST[$field->name])){
                    $item[$field->name] = $_POST[$field->name];
                }elseif(isset($item[$field->name])){ //do not change fields that we are not getting in post
                    unset($item[$field->name]);
                }

                /* Custom logic for custom types */
                if($field->type == APP_Field::$TYPE_PASSWORD){
                    $item[$field->name] = md5($item[$field->name]);
                }
                elseif ($field->type == APP_Field::$TYPE_IMAGE){
                    //TODO: copy image files.
                    //TODO: resize image files if they are too big.
                }
            }

            if(isset($_POST['id'])){
                APP::$db->updateDataById($page->table, $item, $_POST['id']);
                $item['id'] = $_POST['id'];
            }else{
                $item['id'] = APP::$db->insertData($page->table, $item);
            }

            APP::$request->addSuccess("Lagret!");
            APP::$request->jump(APP::$request->controller_url . "/" . APP::$request->controller_action);

        }elseif($urlArray[0] == "edit"){
            $this->log->debug("Handling action: edit");
            if($page->isList){
                APP::$smarty->display('admin/pages/edit.tpl');
            }
        }elseif($urlArray[0] == "delete") {
            $this->log->debug("Handling action: delete");
            if(isset($item)){
                //TODO: delete all subresources.
                APP::$db->deleteDataById($page->table, $_REQUEST['id']);
                foreach ($page->listFields as $field) {
                    if ($field->type == APP_Field::$TYPE_IMAGE) {
                        //$this->deleteFile($item[$field->name]);
                    }
                }

                APP::$request->addSuccess("Successfully deleted");
                APP::$request->jump(APP::$request->controller_url . "/" . APP::$request->controller_action);
            }

        }else{
            $action = array_shift($urlArray); //remove this unknown action from urlArray list (ignore)
            $this->log->debug("Unknown action: " . $action . ", ignoring it..");
            $this->handlePage($urlArray, $page); //restart processing the request.
        }
    }

    function handleConfig($urlArray = [], $page){
        //TODO: render config view
    }

    /**
     * @param array $urlArray array
     * @param $page APP_Page
     */
    function handleList($urlArray = [], $page){
        $params = array();
        $params['from'] = $page->table;
        if($page->belongsTo){
            //TODO: fix when something belongs to another element.
            $params['where'] = array('belongsTo'=>$page->belongsTo);
        }
        $params['orderby'] = $page->orderby;
        $params['limit'] = '1000';

        $items = APP::$db->getData($params);

        if($items){
            APP::$smarty->assign('items', $items);
        }

        APP::$smarty->display('admin/pages/list.tpl');

    }
}