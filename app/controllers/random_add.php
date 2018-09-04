<?php

class Random_addController extends BasicController
{

    var $timeoutTime=50;

    public function pre_filter(&$methodName = null)
    {
        parent::pre_filter($methodName);

        $this->view->addInternalJs("jquery-1.7.1.min.js");
        $this->view->addInternalJs("jquery-ui-1.8.17.custom.min.js");
        $this->view->addInternalCss("ui-lightness/jquery-ui-1.8.17.custom.css");

        $this->set("timeout_time",$this->timeoutTime);
    }

    public function index()
    {


        if(isset($_GET["c"])){
            $_SESSION["c"]=intval($_GET["c"]);
        }

        if(!isset($_SESSION["c"])){
            $_SESSION["c"]=20;
        }

        $this->set("rest",$_SESSION["c"]);

    }

    public function new_question()
    {


        $min = 100;
        $max = 1000;
        $total = rand($min, $max);
        $v1 = rand($min - 10, $total);
        $v2 = $total - $v1;

        $this->set('v1', $v1);
        $this->set('v2', $v2);


    }

    public function ajax()
    {
        $this->setLayout("ajax.phtml");
        $this->set("exa3", "Hello World, ajax");
    }

    public function answer()
    {
        $v1=$_POST['v1'];
        $v2=$_POST['v2'];
        $result=trim($_POST['result']);

        $a=$v1+$v2;

        if( $a != $result){
            $this->set("message", $v1." + ". $v2." Is ".$a." Not ".$result."");
            $_SESSION["c"]=$_SESSION["c"]+2;
        }
        else{
            $this->set("message", "Good!, Your answer is correct");
            $_SESSION["c"]=$_SESSION["c"]-1;
        }
        $this->set("rest",$_SESSION["c"]);
    }
    public function timeout()
    {


        $_SESSION["c"]=$_SESSION["c"]+2;

        $this->set("rest",$_SESSION["c"]);
    }

}

?>