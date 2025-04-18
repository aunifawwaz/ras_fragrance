<?php
include 'db_connection.php';
// Start session or include other necessary PHP setup if needed
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    if (isset($_POST['primary_scent'])) { 
        $_SESSION['primary_scent'] = $_POST['primary_scent']; 
        header("Location: quiz_intensity.php"); 
        exit(); } 
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - Ras Fragrance</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ensure the correct path to styles.css -->
    
</head>
<body>
 

<style>
        /* Updated styles for header layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #8C3F49;
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
            position: relative; /* To position icons and nav on top */
        }

        header .top-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .logo img {
            width: 100px; /* Reduce the size */
            height: auto;
            cursor: pointer; /* Prevents any inline alignment issues */
}



        h1, h2 {
            margin: 5px 0;
            text-align: center;
        }

        nav {
            margin: 10px 0;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            text-decoration: none;
            color: black;
            font-weight: 500;
        }

        .icons {
            display: flex;
            gap: 15px;
        }

        .icons img {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        main {
            padding: 20px;
        }
        .quiz-section {
        text-align: center;
        margin: 20px 0;
    }

    .quiz-section h2 {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .quiz-question {
        margin: 15px 0;
    }

    .quiz-option {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin: 10px 0;
    }

    .quiz-option button {
        padding: 10px 20px;
        background-color: #8C3F49;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .quiz-option button:hover {
        background-color: #722f3a;
    }


    </style>
   <?php include 'header.php' ?>

    <main> 
        <section class="quiz-section"> 
            <h2>Know Your Fragrance</h2> 
            <form method="POST" action="quiz.php"> 
                <div class="quiz-question"> 
                    <label>What is your primary scent preference?</label> 
                    <div class="quiz-option"> 
                        <button type="submit" name="primary_scent" value="floral">Floral</button> 
                        <button type="submit" name="primary_scent" value="woody">Woody</button> 
                        <button type="submit" name="primary_scent" value="citrus">Citrus</button> 
                    </div> 
                </div> 
            </form> 
        </section> 
    </main>

    <?php include('footer.php'); ?>
</body>
</html>

