<?php 
    session_start();
    if ($_SESSION["usergroup"] !== 1)
    {
        header("Location:./index.php"); 
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            include_once "./includes.inc.php";
        ?>
        <!-- Dropdown JS -->
        <script defer src="./javascript/admin.js" ></script>
    </head>
    <body>
        <?php
            include_once "./nav.inc.php";
        ?>
        <main class="container-fluid">
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST")
                {
                    $errorMessage = "";
                    
                    $courseName = $courseDescription = $courseURL = "";
                    
                    switch ($_POST["submit"]) {
                        case "add_course":
                            if (empty($_POST["cname"]))
                            {
                                $errorMessage .= "Course Name is required.<br>";
                            }
                            else
                            {
                                $courseName = sanitizeInput($_POST["cname"]);
                            }
                            
                            if (empty($_POST["cdesc"]))
                            {
                                $errorMessage .= "Course Name is required.<br>";
                            }
                            else
                            {
                                $courseDescription = sanitizeInput($_POST["cdesc"]);
                            }
                            
                            if(preg_match('/^.*\.(mp4|mov)$/i', $_FILES["cfile"]["name"]) && empty($errorMessage)) 
                            {
                                $errorMessage = addCourse($courseName, $courseDescription, $_FILES["cfile"]);
                            }
                            else
                            {
                                $errorMessage .= "Please upload only .MP4 files.\\n";
                            }
                            break;
                        case "delete_course":
                            $errorMessage = deleteCourse($_POST["selectedCourseName"]);
                            break;
                        case "update_course":
                            if (empty($_POST["cname"]))
                            {
                                $errorMessage .= "Course Name is required.<br>";
                            }
                            else
                            {
                                $courseName = sanitizeInput($_POST["cname"]);
                            }
                            
                            if (empty($_POST["cdesc"]))
                            {
                                $errorMessage .= "Course Name is required.<br>";
                            }
                            else
                            {
                                $courseDescription = sanitizeInput($_POST["cdesc"]);
                            }
                            
                            if (empty($_POST["curl"]))
                            {
                                $errorMessage .= "Course URL is required.<br>";
                            }
                            else
                            {
                                $courseURL = sanitizeInput($_POST["curl"]);
                            }
                            
                            if (empty($errorMessage))
                            {
                                $errorMessage = updateCourse($_POST["selectedCourseName"], $courseName, $courseDescription, $courseURL);
                            }
                            break;
                        case "delete_review":
                            $errorMessage = deleteReview($_POST["selectedContent"]);
                            break;
                    }
                                     
                    if (empty($errorMessage))
                    {
                        echo "<script type='text/javascript'>
                                        $(document).ready(function(){
                                        $(\"#modal-message\").html(\"Operation succeeded!\");
                                    });
                                    </script>";
                        echo "<script type='text/javascript'>
                                    $(document).ready(function(){
                                    $('#myModal').modal('show');
                                });
                                </script>";
                        //echo "<script>alert(\"Operation succeeded!\");</script>";
                    }
                    else
                    {
                        echo "<script type='text/javascript'>
                                        $(document).ready(function(){
                                        $(\"#modal-message\").html(\"" . $errorMessage . "\");
                                    });
                                    </script>";
                        echo "<script type='text/javascript'>
                                    $(document).ready(function(){
                                    $('#myModal').modal('show');
                                });
                                </script>";
                    }
                }
            ?>
            <header>
                <h1>Admin</h1>
                <p>This admin dashboard allows you administrative actions for the Dolphin Academy system.</p>
            </header>
            <div class="row">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                      <button class="nav-link active" id="nav-courses-tab" data-bs-toggle="tab" data-bs-target="#nav-courses" type="button" role="tab" aria-controls="nav-courses" aria-selected="true">Courses</button>
                      <button class="nav-link" id="nav-reviews-tab" data-bs-toggle="tab" data-bs-target="#nav-reviews" type="button" role="tab" aria-controls="nav-reviews" aria-selected="false">Reviews</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-courses" role="tabpanel" aria-labelledby="nav-courses-tab">
                        <div class="row">
                            <div class="col-auto">
                                <div class="card medium-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Add Course</h5>
                                        <p class="card-text">This function allows you to add courses.</p>
                                        <form name="add_course_form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data" method="post"> 
                                            <div class="form-group">
                                                <label for="cname">Course Name:</label>
                                                <input class="form-control" type="text" id="cnameAdd" maxlength="45" name="cname" 
                                                        placeholder="Enter product name">
                                            </div>

                                            <div class="form-group">
                                                <label for="cdesc">Course Description:</label>
                                                <textarea class="form-control" id="cdescAdd" maxlength="255" name="cdesc" 
                                                        placeholder="Enter product description"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="cfile">Course Video (.MP4 only):</label>
                                                <input type="file" class="form-control-file" id="cfile" name="cfile">
                                            </div>

                                            <div class="form-group">
                                                <button class="btn btn-primary" type="submit" value="add_course" name="submit">Add Course</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card small-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Delete Course</h5>
                                        <p class="card-text">This function allows you to delete course.</p>
                                        <form name="delete_course_form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">  
                                            <div class="form-group">
                                                <label for="cname">Course Name:</label>
                                                <select class="form-select" id="cnameDelete" name="selectedCourseName">
                                                    <?php 
                                                        $errorMessage = populateCoursesDropDown();

                                                        if (!empty($errorMessage))
                                                        {
                                                            echo "<script>alert(\"" . $errorMessage . "\");</script>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <button class="btn btn-primary" type="submit" value="delete_course" name="submit">Delete Course</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card medium-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Update Course</h5>
                                        <p class="card-text">This function allows you to update courses.</p>
                                        <form name="delete_course_form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">  
                                            <div class="form-group">
                                                <label for="pname">Course Name:</label>
                                                <select class="form-select" id="selectedCourseNameUpdate" name="selectedCourseName" onchange="getCourseDetails(this)">
                                                    <option>Please select a course</option>
                                                    <?php 
                                                        $errorMessage = populateCoursesDropDown();

                                                        if (!empty($errorMessage))
                                                        {
                                                            echo "<script>alert(\"" . $errorMessage . "\");</script>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="cname">Course Name:</label>
                                                <input class="form-control" type="text" id="cnameUpdate" maxlength="45" name="cname" 
                                                        placeholder="Enter course name">
                                            </div>

                                            <div class="form-group">
                                                <label for="cdesc">Course Description:</label>
                                                <textarea class="form-control" id="cdescUpdate" maxlength="255" name="cdesc" 
                                                        placeholder="Enter course description"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="curl">Course URL:</label>
                                                <textarea class="form-control" id="curlUpdate" maxlength="255" name="curl" 
                                                        placeholder="Enter course URL"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <button class="btn btn-primary" type="submit" value="update_course" name="submit">Update Course</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-reviews" role="tabpanel" aria-labelledby="nav-reviews-tab">
                        <div class="row">
                            <div class="col-auto">
                                <div class="card medium-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Delete Review</h5>
                                        <p class="card-text">This function allows you to delete review by content.</p>
                                        <form name="delete_course_form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">  
                                            <div class="form-group">
                                                <label for="pname">Review Content:</label>
                                                <select class="form-select" id="selectedContentUpdate" name="selectedContent">
                                                    <option>Please select a review</option>
                                                    <?php 
                                                        $errorMessage = populateReviewsDropdown();

                                                        if (!empty($errorMessage))
                                                        {
                                                            echo "<script>alert(\"" . $errorMessage . "\");</script>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <button class="btn btn-primary" type="submit" value="delete_review" name="submit">Delete Review</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="adminModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Dolphin Academy</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="modal-message">TEST</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <?php
        include_once "./footer.inc.php"; 
    ?>
</html>