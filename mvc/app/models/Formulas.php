<?php

class Formulas
{
    private $answers_left;
    protected function get_answers_left(){
        return $this->answers_left;
    }
    protected function can_submit_question_formula($all_questions,$right_answers){
        $diff=10;
        if($all_questions>0){
            $right_answers=$right_answers-$diff;
            $all_questions=$all_questions-1;
        }
        if($all_questions>0){
            $right_answers=$right_answers-$diff;
           $all_questions=$all_questions-1;
        }   
        while($all_questions>0){
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
}

