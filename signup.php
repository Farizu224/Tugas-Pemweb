<section class="vh-100">
<head>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>  
<body>
  <div class="container py-5 h-100">
    <div class="row d-flex align-items-center justify-content-center h-100">
      <div class="col-md-8 col-lg-7 col-xl-6">
        <img src="gambar/loginimg.png"
          class="img-fluid" alt="loginimg.png">
      </div>
      <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
        <form method="POST" action="createAccount.php">
          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="Masukkan Email" required/>
            <label class="form-label" for="email">Email address</label>
            <span id="emailError" class="text-danger" style="display: none; top: 40px;">Email sudah terdaftar</span>
          </div>

          <!-- Form untuk Password dan Konfirmasi Password -->
          <div class="form-outline mb-4">
              <input type="password" id="pwd" name="password" class="form-control form-control-lg" placeholder="Masukkan Password" required />
              <label class="form-label" for="password">Password</label>
          </div>
          <div class="form-outline mb-4">
              <input type="password" id="confirmPassword" name="confirmpwd" class="form-control form-control-lg" placeholder="Konfirmasi Password" required />
              <label class="form-label" for="confirmPassword">Konfirmasi Password</label>
              <span id="passwordError" class="text-danger" style="display: none;">Password tidak cocok</span>
          </div>

          <!-- Tombol Sign Up -->
          <button id="signUpButton" type="submit" data-mdb-button-init data-mdb-ripple-init class="signUpButton btn btn-primary btn-lg btn-block d-block w-100">Sign up</button>
          <div style="display: flex; justify-content: center; margin-top: 10px;"> 
            <a style="color:blue; text-decoration:none;" href="login.php">Already have account?</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="js/validate.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</section>