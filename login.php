<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>  

<body>
  <section class="vh-100">
    <div class="container py-5 h-100">
      <div class="row d-flex align-items-center justify-content-center h-100">
        <div class="col-md-8 col-lg-7 col-xl-6">
          <img src="gambar/loginimg.png" class="img-fluid" alt="Login Image">
        </div>
        <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
          <form method="POST" action="check.php" onsubmit="return validateLogin()">
            
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']); // Hapus sesi setelah ditampilkan
            }
            ?>

            <!-- Email input -->
            <div class="form-outline mb-4">
              <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="Masukkan Email" required />
              <label class="form-label" for="email">Email address</label>
              <span id="emailError" class="text-danger" style="display: none;">Email tidak valid</span>
            </div>

            <!-- Password input -->
            <div class="form-outline mb-4">
              <input type="password" id="password" name="pwd" class="form-control form-control-lg" placeholder="Masukkan Password" required />
              <span id="passwordError" class="text-danger" style="display: none;">Password tidak boleh kosong</span>
            </div>

            <div class="d-flex justify-content-around align-items-center mb-4">
              <!-- Checkbox -->
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="form1Example3" checked />
                <label class="form-check-label" for="form1Example3"> Remember me </label>
              </div>
              <a style="color:blue; text-decoration:none;" href="lupapwd.php">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block d-block w-100">Sign in</button>
            <div class="divider d-flex align-items-center mt-4 mb-0">
              <p class="text-center fw-bold mx-3 mb-0 text-muted">Don't have an account?</p>
            </div>

            <a class="signUpButton btn btn-lg btn-block d-block w-100" style="background-color: #3b5998; color: #ffffff;" href="signup.php" role="button">
              Sign up
            </a>
          </form>
        </div>
      </div>
    </div>
  </section>

  <script src="js/validateLogin.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
