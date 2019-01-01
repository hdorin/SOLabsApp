<header>
    <p class="user">Logged in as: <?php echo $_SESSION["user"];?></p>
    <a href="home" class="btnHome">Home</a>
    <a href="choose_chapter" class="btnChooseChapter">Choose Chapter</a>
    <a href="view_questions/1" class="btnMyQuestions"><?php echo $_SESSION['is_admin']==true ? "All Questions" :  "My Questions";?></a>
    <a href="submit_question" class="btnSubmitQuestion">Submit Question</a>
    <a href="logout" class="btnLogout">Logout</a>    
</header>
