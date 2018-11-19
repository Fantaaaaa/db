<?php 
	session_start();

	// variable declaration
	$username = "";
	$userID = "";
	$email    = "";
	$errors = array(); 
	$_SESSION['success'] = "";

	// connect to database
	$db = mysqli_connect('127.0.0.1', 'root', '1loveavril', 'oingo');

	if(!$db){
        die("Connection failed: " . mysqli_connect_error());
    }

	// REGISTER USER
	if (isset($_POST['reg_user'])) {
		// receive all input values from the form
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
		$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

		// form validation: ensure that the form is correctly filled
		if (empty($username)) { array_push($errors, "Username is required"); }
		if (empty($email)) { array_push($errors, "Email is required"); }
		if (empty($password_1)) { array_push($errors, "Password is required"); }

		if ($password_1 != $password_2) {
			array_push($errors, "The two passwords do not match");
		}

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email format is wrong!");
        }

		// register user if there are no errors in the form
		if (count($errors) == 0) {
			$password = md5($password_1);//encrypt the password before saving in the database
			$query = "INSERT INTO user (uname, upassword, uemail) 
					  VALUES('$username', '$password','$email')";
			$result = mysqli_query($db, $query);

			$getNewQuery = "select max(uid) as uid from user";
			$IDResult = mysqli_query($db, $getNewQuery);
            $max = mysqli_fetch_assoc($IDResult);


            if(!$result) {
                array_push($errors, "Register fail");
            }
		}
	}


	// LOGIN USE
	if (isset($_POST['login_user'])) {
		$userID = mysqli_real_escape_string($db, $_POST['userID']);
		$password = mysqli_real_escape_string($db, $_POST['password']);

		if (empty($userID)) {
			array_push($errors, "UserID is required");
		}
		if (empty($password)) {
			array_push($errors, "Password is required");
		}

		if (count($errors) == 0) {
			$password = md5($password);
			$query = "SELECT * FROM user WHERE uid='$userID' AND upassword='$password'";
			$results = mysqli_query($db, $query);

			$getNameQuery = "SELECT uname FROM user WHERE uid='$userID'";
			$nameResult = mysqli_query($db, $getNameQuery);
            $userName = mysqli_fetch_assoc($nameResult);


			if (mysqli_num_rows($results) == 1) {
				$_SESSION['username'] = $userName['uname'];
				header('location: index.php');
			}else {
				array_push($errors, "Wrong userID/password");
			}
		}
	}

?>