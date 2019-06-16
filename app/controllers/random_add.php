<?php

class Random_addController extends BasicController
{

    var $timeoutTime = 50;

    var $one_rest_time = 10;

    public function pre_filter(&$methodName = null)
    {
        parent::pre_filter($methodName);

        $this->view->addInternalJs("jquery-1.7.1.min.js");
        $this->view->addInternalJs("jquery-ui-1.8.17.custom.min.js");
        $this->view->addInternalCss("ui-lightness/jquery-ui-1.8.17.custom.css");


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
            $_SESSION["rest_time"] = $_SESSION["rest_time"] + $this->timeoutTime;
            $this->set("timeout_time", $_SESSION["rest_time"]);
        } else if (isset($_SESSION["start_time"])) {
            //user refresh page
            $v1 = $_SESSION["v1"];
            $v2 = $_SESSION["v2"];
            $op = $_SESSION["op"];

            $_SESSION["rest_time"] = $_SESSION["rest_time"] - (time() - $_SESSION["start_time"]);

            $this->set("timeout_time", $_SESSION["rest_time"]);
        } else {
            //new question
            $_SESSION["total_finish"] = $_SESSION["total_finish"] + 1;
            $debug = true;
            if (!$debug) {
                $min = 100;
                $max = 1150;
                $total = rand($min, $max);
                $v1 = rand($min - 50, $total);
            } else {
                $total = 2;
                $v1 = 1;
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
            $_SESSION["rest_time"] = $_SESSION["rest_time"] + $this->timeoutTime;
            $this->set("timeout_time", $_SESSION["rest_time"]);
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

        $spend = time() - $_SESSION["start_time"];

        $_SESSION["rest_time"] = $_SESSION["rest_time"] - $spend;

        if ($_SESSION["rest_time"] < 0) {
            $_SESSION["rest_time"] = 0;
        }

        if ($a != $result) {

            $_SESSION["v1"] = $v1;
            $_SESSION["v2"] = $v2;
            $_SESSION["op"] = $op;
            $_SESSION["wrong"] = $v1 . $op . $v2 . " is NOT " . $result . " please do again";
            $_SESSION["c"] = $_SESSION["c"] + 1;
            $_SESSION["total_wrong"] = $_SESSION["total_wrong"] + 1;

        } else {
            $_SESSION["v1"] = $v1;
            $_SESSION["v2"] = $v2;
            $_SESSION["op"] = $op;
            unset($_SESSION["wrong"]);

            $_SESSION["c"] = $_SESSION["c"] - 1;
            $_SESSION["rest_time"] = $_SESSION["rest_time"] + $this->one_rest_time;


        }

        error_log("answer c:" . $_SESSION["c"]);
        //any way, rest the timeout
        unset($_SESSION["start_time"]);

        if ($_SESSION["c"] <= 0) {
            $this->redirect("/random_add/done");
        } else {
            $this->redirect("/random_add/new_question");
        }

    }

    public function timeout()
    {


        $_SESSION["c"] = $_SESSION["c"] + 2;

        $_SESSION["wrong"] = "You did not answer question in " . $this->timeoutTime . " seconds!!";
        //any way, rest the timeout
        unset($_SESSION["start_time"]);
        $this->redirect("/random_add/new_question");
    }


    public function done()
    {

        $this->set('give_size', $_SESSION["question_size"]);
        $this->set('real_size', $_SESSION["total_finish"]);
        $this->set('wrong_size', $_SESSION["total_wrong"]);

        $video = 10 * $_SESSION["rest_time"] / 60 + 1;

        $maxVideo = intval(2 * $_SESSION["question_size"]);
        $minVideo = $_SESSION["question_size"];

        if ($video > $maxVideo) {
            $video = $maxVideo;
        }

        if ($video < $minVideo) {
            $video = $minVideo;
        }


        $this->set('total_rest_second', $_SESSION["rest_time"]);
        $this->set('video_minutes', $video);


    }

}

?>