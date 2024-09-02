<?php
require 'PHP/Connection.php';

if(isset($_POST['submit'])){
    $file_name = $_FILES['image']['name'];
    $tempname = $_FILES['image']['tmp_name'];
    $folder = "Images/".$file_name;

    if(move_uploaded_file($tempname, $folder)){
        $query = mysqli_query($conn, "INSERT INTO images (file) VALUES ('$file_name')");
        if($query){
            echo "<h2>Image uploaded successfully </h2>";
        }else{
            echo "<h2>Failed to upload image</h2>";
        }
    }else{
        echo "<h2>Failed to move uploaded file</h2>";
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="image">
        <input type="submit" name="submit" value="Upload">
    </form>

    <form method="POST">
        <label for="image_id">Enter Image ID: </label>
        <input type="text" name="image_id" id="image_id">
        <input type="submit" name="retrieve" value="Retrieve Image">
    </form>

    <div>
    <?php
    if(isset($_POST['retrieve'])){
        $image_id = $_POST['image_id'];
        $res = mysqli_query($conn, "SELECT * FROM images WHERE id = $image_id");
        $row = mysqli_fetch_array($res);
        if($row){
            echo "<img src='Images/".$row['file']."' >";
        }else{
            echo "<h2>No image found with that ID</h2>";
        }
    } ?>
    </div>
</body>
</html>