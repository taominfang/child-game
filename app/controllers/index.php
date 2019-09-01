<?php

class IndexController extends BasicController
{

    private $password = "4455";

    public function index()
    {


        $this->set("title", "I am Index page");
        $this->set('message', 'hellow');


    }

    public function parent_index()
    {

        if(isset($_REQUEST["error"])){
            if ($_REQUEST["error"] =="p"){
                $this->set("error_message","password error");
            }
        }
    }

    public function pause()
    {

    }

    public function stop_pause()
    {
        unset($_SESSION["pause"] );
        $this->redirect("/index");

    }

    public function parent_control()
    {
        if ($this->password ===$_POST['password'] ) {


            $op_type = $_POST['op_type'];

            if ($op_type == "pause") {
                $_SESSION["pause"] = 1;
                $this->redirect("/index/pause");
            } else if ($op_type == "question_size") {

                $qs = intval($_POST['question_size']);
                if ($qs > 1) {
                    $_SESSION["c"] = $qs;
                    $_SESSION["rest_time"] = 40;
                    $_SESSION["question_size"] = $qs;
                    $_SESSION["total_finish"] = 0;
                    $_SESSION["total_wrong"] = 0;

                    unset($_SESSION["start_time"]);
                    unset($_SESSION["wrong"]);

                }
                $this->redirect("/index");
            } else {
                $this->redirect("/index");
            }

        }
        else{

            $this->redirect("/index/parent_index?error=p");
        }
    }


}

?>