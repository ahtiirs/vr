<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">RIF20 Veebirakendused</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="home.php">Galerii <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="show_news.php">Uudised</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Seaded
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="add_news.php">Lisa uudis</a>
          <a class="dropdown-item" href="upload_photo.php">Lisa pilt</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="add_user.php">Lisa kasutaja</a>
        </div>
      </li>
      <!-- <li class="nav-item">
        <a class="nav-link disabled" href="#">keelatud</a>
      </li> -->
    </ul>
    <a <?PHP if(!isset($_SESSION["user_id"])): ?>
              href="page.php">Logi sisse
            <?php else: echo $_SESSION["user_first_name"]." ".$_SESSION["user_last_name"];?>
              href="home.php?logout=1"> <?php echo $_SESSION["user_first_name"]." ".$_SESSION["user_last_name"]." / "; ?>Logi välja
            <?php endif ?></a> 
    <!-- <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="otsin miskit" placeholder="Otsin siit..." aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">OTSI</button> -->
      <!-- <a href="?logout=1"><?php echo $_SESSION["user_first_name"]." ".$_SESSION["user_last_name"]." / "; ?> Logi välja</a> -->
    <!-- </form> -->
  </div>
</nav>