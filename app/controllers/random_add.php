<?php

class Random_addController extends BasicController
{

    var $timeoutTime = 50;

    public function pre_filter(&$methodName = null)
    {
        parent::pre_filter($methodName);

        $this->view->addInternalJs("jquery-1.7.1.min.js");
        $this->view->addInternalJs("jquery-ui-1.8.17.custom.min.js");
        $this->view->addInternalCss("ui-lightness/jquery-ui-1.8.17.custom.css");


        if (!isset($_SESSION["c"])) {
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
            $min = 2;
            $max = 20;
            $total = rand($min, $max);
            $v1 = rand($min - 1, $total);


            if (rand() % 2 == 0) {
                $op = "+";
                $v2 = $total - $v1;
            } else {
                $op = "-";
                $v2 = $v1;
                $v1=$total;

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
            $_SESSION["c"] = $_SESSION["c"] + 2;

        } else {
            $_SESSION["v1"] = $v1;
            $_SESSION["v2"] = $v2;
            $_SESSION["op"] = $op;
            unset($_SESSION["wrong"]);

            $_SESSION["c"] = $_SESSION["c"] - 1;

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

}

?>