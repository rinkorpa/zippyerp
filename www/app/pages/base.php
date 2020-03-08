<?php

namespace App\Pages;

use Zippy\Binding\PropertyBinding;
use Zippy\Html\Label;
use Zippy\Html\Panel;
use Zippy\Html\Link\ClickLink;
use App\Helper;
use App\Application as App;
use App\System;
use App\Entity\User;

class Base extends \Zippy\Html\WebPage
{

    public function __construct($params = null) {
        global $_config;


        \Zippy\Html\WebPage::__construct();


        $user = System::getUser();
        if ($user->user_id == 0) {
            App::Redirect("\\App\\Pages\\Userlogin");
            return;
        }

        $this->add(new ClickLink('logout', $this, 'LogoutClick'));
        $this->add(new Label('username', $user->username));

 
        $this->add(new ClickLink("pageinfo"));
      
        $options = System::getOptions('common');


        $this->_tvars["docmenu"]  = Helper::generateMenu(1);
        $this->_tvars["repmenu"]  = Helper::generateMenu(2);
        $this->_tvars["regmenu"]  = Helper::generateMenu(3);
        $this->_tvars["refmenu"]  = Helper::generateMenu(4);
       

        $this->_tvars["islogined"] = $user->user_id > 0;
        $this->_tvars["isadmin"] = $user->userlogin == 'admin';
        $pi = $this->getPageInfo();

        if (strlen($pi) == 0) {
            $this->pageinfo->setVisible(false);
        }


        $this->_tvars["defoptions"] = ($options['defstore'] > 0  )  == false;


        $this->_tvars["smart"] = Helper::generateSmartMenu();
        $this->_tvars["picontent"] = $pi;
        

        if (Helper::usends()) {
            $this->_tvars["usends"] = true;
            $this->_tvars["_nds"] = Helper::nds();
            $this->_tvars["nds_"] = Helper::nds(true);
        } else {
            $this->_tvars["usends"] = false;
            $this->_tvars["_nds"] = 0;
            $this->_tvars["nds_"] = 0;
        }
    }

    public function LogoutClick($sender) {
        setcookie("remember", '', 0);
        System::setUser(new \App\Entity\User());
        $_SESSION['user_id'] = 0;
        $_SESSION['userlogin'] = 'Гость';

        //$page = $this->getOwnerPage();
        //  $page = get_class($page)  ;
        App::Redirect("\\App\\Pages\\UserLogin");
        ;
        ;
        //    App::$app->getresponse()->toBack();
    }

    public function getPageInfo() {
        $class = explode("\\", get_class($this));
        $classname = $class[count($class) - 1];
        return \App\Helper::getMetaNotes($classname);
    }

    //вывод ошибки,  используется   в дочерних страницах
    public function setError($msg) {
        System::setErrorMsg($msg);
    }

    public function setSuccess($msg) {
        System::setSuccesMsg($msg);
    }

    public function setWarn($msg) {
        System::setWarnMsg($msg);
    }

    public function setInfo($msg) {
        System::setInfoMsg($msg);
    }

    final protected function isError() {
        return strlen(System::getErrorMsg()) > 0;
    }

  
    public function beforeRender() {
        $user = System::getUser();
        $this->_tvars['notcnt'] = \App\Entity\Notify::isNotify($user->user_id);
        
        $this->_tvars['alerterror'] = "";
        $this->_tvars['alertwarning'] = "";
        $this->_tvars['alertsuccess'] = "";
        $this->_tvars['alertinfo'] = "";

        if (strlen(System::getErrorMsg()) > 0) {
            $this->_tvars['alerterror'] = System::getErrorMsg();
            $this->goAnkor('');
        }
        if (strlen(System::getWarnMsg()) > 0) {
            $this->_tvars['alertwarning'] = System::getWarnMsg();
            $this->goAnkor('');
        }
        if (strlen(System::getSuccesMsg()) > 0) {
            $this->_tvars['alertsuccess'] = System::getSuccesMsg();
            $this->goAnkor('');
        }
        if (strlen(System::getInfoMsg()) > 0) {
            $this->_tvars['alertinfo'] = System::getInfoMsg();
            $this->goAnkor('');
        }  
    }      
   

    protected function afterRender() {
        $this->setError('');
        $this->setSuccess('');

        $this->setInfo('');
        $this->setWarn('');
    }

    //Перезагрузить страницу  с  клиента
    //например для  сброса  адресной строки  после  команды удаления
    protected final function resetURL() {
        \App\Application::$app->setReloadPage();
    }

}
