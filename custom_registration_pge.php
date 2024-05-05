<?php
/*
Template Name:Registration Form
*/
include 'C:\xampp\htdocs\migrate_wordpress\wp-load.php';


// get_header();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<style>
    /* Styles for the registration form */
#registration-form {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
}

#registration-form label {
    display: block;
    margin-bottom: 10px;
}

#registration-form input[type="text"],
#registration-form input[type="email"],
#registration-form input[type="password"],
#registration-form select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 3px;
}

#registration-form input[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

/* Responsive styles */
@media screen and (max-width: 600px) {
    #registration-form {
        width: 90%;
    }
}

</style>
</head>
<body>
    
</body>
</html>
<form id="registration-form" action="<?php echo admin_url('admin-post.php') ; ?>" method="post">
    <input type="hidden" name="action" value="custom_user_registration">
    <label for="username">Username:</label>
    <input type="text" name="username" required>
    <label for="email">Email:</label>
    <input type="email" name="email" required>
    <label for="password">Password:</label>
    <input type="password" name="password" required>
    <label for="role">Role:</label>
    <select name="role" required>
        <option value="subscriber">Subscriber</option>
        <option value="contributor">Contributor</option>
        <option value="author">Author</option>
        <option value="editor">Editor</option>
        <option value="administrator">administrator</option>
    </select>
    <input type="submit" value="Register">
</form>
