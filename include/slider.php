<!--thumnail-->
    
<div class="container">
      <div class="content-container">
        <div
          class="featured-content"
          style="
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), #151515),
              url('images/slider/4k-marvel-and-dc-vector-art-pmkg7yqt3zz8bmcc.jpg');
          "
        >
          <p class="featured-desc">WELCOME TO THE MARVEL - DC MOVIE WORLD!</p>
        </div>
      </div>
        <!--VŨ TRỤ MARVEl-->

        <div class="movie-list-container">
          <div class="movie-list-header">
            <h1 class="movie-list-title">VŨ TRỤ MARVEL (MCU)</h1>
            <a href="index.php?page=marvel" class="iq-view-all"
              ><b><u>View All</u></b></a
            >
          </div>
          <?php 
          $sql_marvel = "SELECT * FROM movies WHERE universe_id = '1' ORDER BY id DESC";
          $result_marvel = mysqli_query($conn, $sql_marvel);

          if (mysqli_num_rows($result_marvel) > 0) {
          ?>

          <div class="movie-list-wrapper">
            <div class="movie-list">

              <?php while($row_marvel = mysqli_fetch_assoc($result_marvel)) { ?>
                <div class="movie-list-item">
                  <img
                    class="movie-list-item-img"
                    src="<?php echo $row_marvel['thumbnail']; ?>"
                    alt=""
                  />
                  <span class="movie-list-item-title"><?php echo $row_marvel['title']; ?></span>
                  <a href="index.php?page=watch&id=<?php echo $row_marvel['id']; ?>"><button class="movie-list-item-button">XEM NGAY</button>
                  </a>
                </div>
              <?php } ?>

            </div>
            <i class="fas fa-chevron-right arrow"></i>
          </div>

          <?php 
          } else {
            echo "Không có phim nào";
          }
          ?>
          </div>
        <!--END DIV TỔNG-->

        <!--Thumnail-->
        <div
          class="featured-content"
          style="
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), #151515),
              url('img/f-2.jpg');
          "
        >
          <p class="featured-desc">
            CHÚC BẠN XEM PHIM VUI VẺ!
          </p>
        </div>
        <!--VŨ TRỤ DC-->

        <div class="movie-list-container">
          <div class="movie-list-header">
            <h1 class="movie-list-title">VŨ TRỤ DC (DCU)</h1>
            <a href="index.php?page=dcu" class="iq-view-all"
              ><b><u>View All</u></b></a
            >
          </div>
          <?php 
          $sql_marvel = "SELECT * FROM movies WHERE universe_id = '2' ORDER BY id DESC";
          $result_marvel = mysqli_query($conn, $sql_marvel);

          if (mysqli_num_rows($result_marvel) > 0) {
          ?>

          <div class="movie-list-wrapper">
            <div class="movie-list">

              <?php while($row_marvel = mysqli_fetch_assoc($result_marvel)) { ?>
                <div class="movie-list-item">
                  <img
                    class="movie-list-item-img"
                    src="<?php echo $row_marvel['thumbnail']; ?>"
                    alt=""
                  />
                  <span class="movie-list-item-title"><?php echo $row_marvel['title']; ?></span>
                  <a href="index.php?page=watch&id=<?php echo $row_marvel['id']; ?>"><button class="movie-list-item-button">XEM NGAY</button>
                  </a>
                </div>
              <?php } ?>

            </div>
            <i class="fas fa-chevron-right arrow"></i>
          </div>

          <?php 
          } else {
            echo "Không có phim nào";
          }
          ?>
          </div>
        <!--END DIV TỔNG-->

        <!--Top phim-->

        <div class="movie-list-container">
          <div class="movie-list-header">
            <h1 class="movie-list-title">PHIM NỔI BẬT</h1>
            <a href="index.php?page=featured" class="iq-view-all"
              ><b><u>View All</u></b></a
            >
          </div>
          <?php 
          $sql_marvel = "SELECT * FROM movies WHERE featured = '1' ORDER BY id DESC";
          $result_marvel = mysqli_query($conn, $sql_marvel);

          if (mysqli_num_rows($result_marvel) > 0) {
          ?>

          <div class="movie-list-wrapper">
            <div class="movie-list">

              <?php while($row_marvel = mysqli_fetch_assoc($result_marvel)) { ?>
                <div class="movie-list-item">
                  <img
                    class="movie-list-item-img"
                    src="<?php echo $row_marvel['thumbnail']; ?>"
                    alt=""
                  />
                  <span class="movie-list-item-title"><?php echo $row_marvel['title']; ?></span>
                  <a href="index.php?page=watch&id=<?php echo $row_marvel['id']; ?>"><button class="movie-list-item-button">XEM NGAY</button>
                  </a>
                </div>
              <?php } ?>

            </div>
            <i class="fas fa-chevron-right arrow"></i>
          </div>

          <?php 
          } else {
            echo "Không có phim nào";
          }
          ?>
          </div>
        <!--END DIV TỔNG-->