<?php

class Formulas
{
    private $answers_left;
    public function get_answers_left(){
        return $this->answers_left;
    }
    public function can_submit_question($right_answers,$posted_questions,$deleted_questions,$code_reveals){
        $diff=10;
        $right_answers=$right_answers-($diff*$deleted_questions);//We take into account the times user deleted questions
        $right_answers=$right_answers-3*$code_reveals;//We take into account the times user revealed code
        $right_answers=$right_answers-$diff;//first posted question need 10 right answers
        if($posted_questions>0){
            $right_answers=$right_answers-$diff;//second posted question needs 10 right answers
            $posted_questions=$posted_questions-1;
        }
        while($posted_questions>0){//for more than two questions
            $diff=$diff*2;
            $right_answers=$right_answers-$diff;
            $posted_questions=$posted_questions-1;
        }
        $this->answers_left=$right_answers;
        if($right_answers>0){
            return true;
        }else{
            return false;
        }
    }
    public function can_delete_question($right_answers,$posted_questions,$deleted_questions,$code_reveals){
        $diff=10;
        $right_answers=$right_answers-$diff;//To delete a question answer 10 questions
        $right_answers=$right_answers-3*$code_reveals;//We take into account the times user revealed code
        $right_answers=$right_answers-($diff*$deleted_questions);//We take into account the times user deleted questions
        //We take into account the questions posted by the user
        if($posted_questions>0){
            $right_answers=$right_answers-$diff;//first posted question need 10 right answers
            $posted_questions=$posted_questions-1;
        }
        if($posted_questions>0){
            $right_answers=$right_answers-$diff;//second posted question need 10 right answers
            $posted_questions=$posted_questions-1;
        }
        while($posted_questions>0){//for more than two questions
            $diff=$diff*2;
            $right_answers=$right_answers-$diff;
            $posted_questions=$posted_questions-1;
        }
        $this->answers_left=$right_answers;
        if($right_answers>0){
            return true;
        }else{
            return false;
        }
    }
    public function can_reveal_author_code($right_answers,$posted_questions,$deleted_questions,$code_reveals){
        $diff=10;
        $right_answers=$right_answers-3;//To delete a question answer 10 questions
        $right_answers=$right_answers-3*$code_reveals;//We take into account the times user revealed code
        $right_answers=$right_answers-($diff*$deleted_questions);//We take into account the times user deleted questions
        //We take into account the questions posted by the user
        if($posted_questions>0){
            $right_answers=$right_answers-$diff;//first posted question need 10 right answers
            $posted_questions=$posted_questions-1;
        }
        if($posted_questions>0){
            $right_answers=$right_answers-$diff;//second posted question need 10 right answers
            $posted_questions=$posted_questions-1;
        }
        while($posted_questions>0){//for more than two questions
            $diff=$diff*2;
            $right_answers=$right_answers-$diff;
            $posted_questions=$posted_questions-1;
        }
        $this->answers_left=$right_answers;
        if($right_answers>0){
            return true;
        }else{
            return false;
        }
    }
}

