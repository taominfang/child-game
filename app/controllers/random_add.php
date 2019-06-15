<?php

class Random_addController extends BasicController
{

    var $timeoutTime = 70;

    var $one_rest_time = 10;

    public function pre_filter(&$methodName = null)
    {
        parent::pre_filter($methodName);

        $this->view->addInternalJs("jquery-1.7.1.min.js");
        $this->view->addInternalJs("jquery-ui-1.8.17.custom.min.js");
        $this->view->addInternalCss("ui-lightness/jquery-ui-1.8.17.custom.css");


        if (!isset($_SESSION["c"]) || intval($_SESSION["c"]) <= 0) {
            $this->redirect("/index/parent_index");
            return false;
        }

    }

    public function index()
    {

        $this->redirect("/random_add/new_question");

    }

    public function new_question()
    {


        if (isset($_SESSION["wrong"])) {
            //wrong, repeat
            $v1 = $_SESSION["v1"];
            $v2 = $_SESSION["v2"];
            $op = $_SESSION["op"];
            $this->set("error_message", $_SESSION["wrong"]);
            $_SESSION["start_time"] = time();
            unset($_SESSION["wrong"]);
            $this->set("timeout_time", $this->timeoutTime);
        } else if (isset($_SESSION["start_time"])) {
            //user refresh page
            $v1 = $_SESSION["v1"];
            $v2 = $_SESSION["v2"];
            $op = $_SESSION["op"];

            $timeout = $this->timeoutTime - (intval(time()) - intval($_SESSION["start_time"]));
            $this->set("timeout_time", $timeout);
        } else {
            //new question
            $debug=false;
            if (!$debug){
                $min = 50;
                $max = 1100;
                $total = rand($min, $max);
                $v1 = rand($min - 10, $total);
            }
            else{
                $total=2;
                $v1=1;
            }



            if (rand() % 2 == 0) {
                $op = "+";
                $v2 = $total - $v1;
            } else {
                $op = "-";
                $v2 = $v1;
                $v1 = $total;

            }
            $_SESSION["v1"] = $v1;
            $_SESSION["v2"] = $v2;
            $_SESSION["op"] = $op;
            $_SESSION["start_time"] = time();
            $this->set("timeout_time", $this->timeoutTime);
        }

        $this->set('v1', $v1);
        $this->set('v2', $v2);
        $this->set('op', $op);
        $this->set('rest', $_SESSION["c"]);
        $this->set('rest_time', $_SESSION["rest_time"]);

    }

    public function ajax()
    {
        $this->setLayout("ajax.phtml");
        $this->set("exa3", "Hello World, ajax");
    }

    public function answer()
    {
        $v1 = $_POST['v1'];
        $v2 = $_POST['v2'];
        $op = $_POST['op'];

        $result = trim($_POST['result']);

        if ($op == "+") {
            $a = $v1 + $v2;
        } else {
            $a = $v1 - $v2;
        }


        if ($a != $result) {

            $_SESSION["v1"] = $v1;
            $_SESSION["v2"] = $v2;
            $_SESSION["op"] = $op;
            $_SESSION["wrong"] = $v1 . $op . $v2 . " is NOT " . $result . " please do again";
            $_SESSION["c"] = $_SESSION["c"] + 1;

        } else {
            $_SESSION["v1"] = $v1;
            $_SESSION["v2"] = $v2;
            $_SESSION["op"] = $op;
            unset($_SESSION["wrong"]);

            $_SESSION["c"] = $_SESSION["c"] - 1;
            $_SESSION["rest_time"] = $_SESSION["rest_time"] + $this->one_rest_time;


        }

        //any way, rest the timeout
        unset($_SESSION["start_time"]);

        $this->redirect("/random_add/new_question");
    }

    public function timeout()
    {


        $_SESSION["c"] = $_SESSION["c"] + 2;

        $_SESSION["wrong"] = "You did not answer question in " . $this->timeoutTime . " seconds!!";
        //any way, rest the timeout
        unset($_SESSION["start_time"]);
        $this->redirect("/random_add/new_question");
    }


    private function recalculate_rest(){

        if (isset($_SESSION["start_rest"])){
            $using=time()-$_SESSION["start_rest"];

            $rest=$_SESSION["rest_time"]-$using;

            if($rest<=0){
                $rest=0;
            }
            $_SESSION["rest_time"]=$rest+1;
        }

    }

    public function rest()
    {
        $this->recalculate_rest();

        if (!isset($_SESSION["at_rest_lock_question_time"])){
            //first , lock the question rest time
            $_SESSION["at_rest_lock_question_time"]=time()-$_SESSION["start_time"];
        }

        $_SESSION["start_rest"]=time();

        $this->set('rest_time', $_SESSION["rest_time"]);

    }

    public function rest_end()
    {

        $this->recalculate_rest();


        //reset start_time
        $_SESSION["start_time"]=time()-$_SESSION["at_rest_lock_question_time"];
        unset($_SESSION["start_rest"]);
        unset($_SESSION["at_rest_lock_question_time"]);
        $this->redirect("/random_add/new_question");
    }

}

?>