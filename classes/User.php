<?php
require_once 'Database.php';

// inherit Database class to allow User class to have connection to the database
class User extends Database
{
     //store() used to store data on db
    public function store($request)
    {

        //$reqest holds all the data from the form. This will catch the value of $_POST from actions/register.php

        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $username = $request['username'];
        $password = $request['password'];

        $password = password_hash($password, PASSWORD_DEFAULT);

        $spl = "INSERT INTO users (first_name, last_name, username, password) VALUES('$first_name', '$last_name', '$username', '$password')";

        if ($this->conn->query($spl)) {
            header('location: ../views');  //go to index.php
            exit;
        } else {
            die('error creating the user: ' . $this->conn->error);
        }
    }

    public function login($reqest)
    {
        $username = $reqest['username'];
        $password = $reqest['password'];

        $spl = "SELECT * FROM users WHERE username = '$username'";

        $result = $this->conn->query($spl);

        //Check the username
        if ($result->num_rows == 1) {
            // cheak if password is correct
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['first_name'] . " " . $user['last_name'];

                header('location: ../views/dashbord.php');
                exit;
            } else {
                die('Password is incorrect.');
            }
        } else {
            die('Username not found');
        }
    }

    public function logout()
    {
        session_start();   //access session variables
        session_unset();   //remove the values of the session variabls
        session_destroy(); //terminate the session

        header('location: ../views');
        exit;
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";

        if ($result = $this->conn->query($sql)) {
            return $result;
        } else {
            die('Error retrieving the users: ' . $this->conn->error);
        }
    }

    public function getUser()
    {
        $id = $_SESSION['id']; //get the ID of the logged in user
        $sql = "SELECT * FROM users WHERE id = $id";

        if ($result = $this->conn->query($sql)) {
            return $result->fetch_assoc();
        } else {
            die('Error retrieving the user: ' . $this->conn->error);
        }
    }

    public function update($request, $files)
    {
        session_start();

        $id         = $_SESSION['id'];
        $first_name = $request['first_name'];
        $last_name  = $request['last_name'];
        $username   = $request['username'];
        $photo      = $files['photo']['name'];
        $tmp_photo  = $files['photo']['tmp_name'];

        $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id = $id";

        if ($this->conn->query($sql)) {
            $_SESSION['username'] = $username;
            $_SESSION['full_name'] = "$first_name $last_name";

            // if there is an uploaded photo, save it to the db and save the file images folder
            if ($photo) {
                $sql = "UPDATE users SET photo = '$photo' WHERE id = $id";
                $destination = "../assets/images/$photo";

                // save the image name to database
                if ($this->conn->query($sql)) {
                    // save the file images folder
                    if (move_uploaded_file($tmp_photo, $destination)) {
                        header("location: ../views/dashboard.php");
                        exit;
                    } else {
                        die("Error moving the photo");
                    }
                } else {
                    die("Error uploading photo: " . $this->conn->error);
                }
            }

            // no photo upload
            header("location: ../views/dashboard.php");
            exit;
        } else {
            die("Error updating the user: " . $this->conn->error);
        }
    }

    public function delete()
    {
        session_start();

        $id = $_SESSION['id'];

        $sql = "DELETE FROM users WHERE id = $id";

        if($this->conn->query($sql)){
            $this->logout();
        }else{
            die('Error deleting your account:' . $this->conn->error);
        }

    }
}
?>