<!DOCTYPE html>
<html>
    <head>
        <?php
            include "includes.inc.php";
        ?>
    </head>
    <body>
        <?php
            include "nav.inc.php";
        ?>
        <header>
            <h1>Reviews</h1>
            <p>To leave a review, please login <a href="./login.php">here</a> first!</p>
        </header>
        <main class="container-fluid">  
            <div class="row">
                <?php
                    require "php/DatabaseFunctions.php";
                    
                    getTestimonials();                    
                ?>
            </div>
            <form class="form-inline">
                <textarea placeholder="Your thoughts go here!" class="form-control" rows="8" cols="100"></textarea>
                <button type="submit" class="btn btn-primary mb-2">Submit</button>
            </form>
        </main>
        <?php
            include "footer.inc.php"; 
        ?>
    </body>
</html>
