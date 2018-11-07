<?php

class Formulas
{
    private $answers_left;
    public function get_answers_left(){
        return $this->answers_left;
    }
    public function can_submit_question_formula($all_questions,$right_answers){
        $diff=10;
        $right_answers=$right_answers-$diff;//first question need 10 right answers
        if($all_questions>0){
            $right_answers=$right_answers-$diff;//second question need 10 right answers
            $all_questions=$all_questions-1;
        }
        while($all_questions>0){//for more than two questions
            $diff=$diff*2;
            $right_answers=$right_answers-$diff;
            $all_questions=$all_questions-1;
        }
        $this->answers_left=$right_answers;
        if($right_answers>0){
            return true;
        }else{
            return false;
        }
    }
    public function can_delete_question($right_answers){
        $diff=10;
        $right_answers=$right_answers-$diff;
        $this->answers_left=$right_answers;
        if($right_answers>0){
            return true;
        }else{
            return false;
        }
    }
}

