<?php require_once __DIR__ . '/includes/helpers.php'; ?>
<?php require_once __DIR__ . '/includes/site_images.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Adatech Solutions | About us</title>
  <meta content="Adatech Solutions is a forward-thinking technology company dedicated to software, embedded systems, and IoT solutions in Ethiopia." name="description">
  <meta content="Adatech Solutions, about, Ethiopia, IoT, embedded, software" name="keywords">

  <!-- Favicons -->
  <link href="<?php echo site_image('favicon'); ?>" rel="icon">
  <link href="<?php echo site_image('apple_touch_icon'); ?>" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600;1,700&family=Roboto:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Work+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: UpConstruction - v1.3.0
  * Template URL: https://bootstrapmade.com/upconstruction-bootstrap-construction-website-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header d-flex align-items-center">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1>Adatech<span>.</span></h1>
      </a>

      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="about.php" class="active">About us</a></li>
          <li><a href="services.php">Services</a></li>
          <li><a href="projects.php">Projects</a></li>
          <li><a href="team.php">Team</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->

  <main id="main">

    <!-- ======= Breadcrumbs ======= -->
    <div class="breadcrumbs d-flex align-items-center" style="<?php echo site_bg('hero_bg'); ?>">
      <div class="container position-relative d-flex flex-column align-items-center" data-aos="fade">

        <h2>About</h2>
        <ol>
          <li><a href="index.php">Home</a></li>
          <li>About</li>
        </ol>

      </div>
    </div><!-- End Breadcrumbs -->

    <!-- ======= About Section ======= -->
    <section id="about" class="about">
      <div class="container" data-aos="fade-up">

        <div class="row position-relative">

          <div class="col-lg-7 about-img" style="<?php echo site_bg('about_main'); ?>"></div>
          <div class="col-lg-7">
            <?php
            // Load about content from DB if available
            require_once __DIR__ . '/includes/db.php';
            $aboutStmt = $pdo->prepare('SELECT title, body FROM content WHERE type = ? LIMIT 1');
            $aboutStmt->execute(['about']);
            $about = $aboutStmt->fetch();
            ?>
            <?php if ($about): ?>
              <h2><?php echo htmlspecialchars($about['title'] ?: 'Who We Are'); ?></h2>
              <div class="our-story">
                <?php echo $about['body']; ?>
              </div>
            <?php endif; ?>
          </div>

        </div>

      </div>
    </section>
    <!-- End About Section -->

    <!-- ======= Stats Counter Section ======= -->
    <section id="stats-counter" class="stats-counter section-bg">
      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center w-100 h-100">
              <i class="bi bi-emoji-smile color-blue flex-shrink-0"></i>
              <div>
                <span data-purecounter-start="0" data-purecounter-end="30" data-purecounter-duration="1"
                  class="purecounter"></span>
                <p>Clients</p>
              </div>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center w-100 h-100">
              <i class="bi bi-journal-richtext color-orange flex-shrink-0"></i>
              <div>
                <span data-purecounter-start="0" data-purecounter-end="50" data-purecounter-duration="1"
                  class="purecounter"></span>
                <p>Projects</p>
              </div>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center w-100 h-100">
              <i class="bi bi-headset color-green flex-shrink-0"></i>
              <div>
                <span data-purecounter-start="0" data-purecounter-end="1000" data-purecounter-duration="1"
                  class="purecounter"></span>
                <p>Hours Of Support</p>
              </div>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center w-100 h-100">
              <i class="bi bi-people color-pink flex-shrink-0"></i>
              <div>
                <span data-purecounter-start="0" data-purecounter-end="10" data-purecounter-duration="1"
                  class="purecounter"></span>
                <p>Experts</p>
              </div>
            </div>
          </div><!-- End Stats Item -->

        </div>

      </div>
    </section><!-- End Stats Counter Section -->

    <!-- Duplicated About section removed per request -->
    <section id="about-2" class="alt-services section-bg">
      <div class="container" data-aos="fade-up">

        <div class="row justify-content-around gy-4">

          <!-- Left Content -->
          <div class="col-lg-5 d-flex flex-column justify-content-center">

            <h3>Our Mission, Vision & Commitment</h3>
            <p>
              At Adatech, we believe technology should improve everyday life.
              Our mission is to build impactful solutions that connect people,
              strengthen industries, and enable Africaâ€™s digital future.
            </p>

            <!-- Icon Box 1 -->
            <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="100">
              <i class="bi bi-bullseye flex-shrink-0"></i>
              <div>
                <h4><a href="#" class="stretched-link">Our Mission</a></h4>
                <p>
                  Deliver practical, accessible, and innovative tech solutions that solve
                  real-world challenges.
                </p>
              </div>
            </div>

            <!-- Icon Box 2 -->
            <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="200">
              <i class="bi bi-eye flex-shrink-0"></i>
              <div>
                <h4><a href="#" class="stretched-link">Our Vision</a></h4>
                <p>
                  To become Africaâ€™s leading hub for hardwareâ€“software integration and
                  smart technology innovation.
                </p>
              </div>
            </div>

            <!-- Icon Box 3 -->
            <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-heart-pulse flex-shrink-0"></i>
              <div>
                <h4><a href="#" class="stretched-link">Our Values</a></h4>
                <p>
                  Innovation, integrity, responsibility, community empowerment, and
                  sustainable development.
                </p>
              </div>
            </div>

            <!-- Icon Box 4 -->
            <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-lightning-charge flex-shrink-0"></i>
              <div>
                <h4><a href="#" class="stretched-link">Our Commitment</a></h4>
                <p>
                  We build tech that lastsâ€”reliable systems, scalable architecture, and
                  human-centered design.
                </p>
              </div>
            </div>

          </div>

          <!-- Right Image -->
          <div class="col-lg-6 img-bg"
            style="background-image: url(<?php echo "'" . site_image('about_main') . "'"; ?>)"
            data-aos="zoom-in" data-aos-delay="100">
          </div>

        </div>

      </div>
    </section>
    <!-- End About Us Section 2 -->


    <!-- ======= Our Team Section ======= -->
    <section id="team" class="team">
      <div class="container" data-aos="fade-up">
        <div class="section-header">
          <h2>Our Team</h2>
          <p>Leadership team at Adatech Solutions</p>
        </div>

        <div class="row gy-5">
          <?php
          // Load team members from DB
          require_once __DIR__ . '/includes/db.php';
          $teamStmt = $pdo->query('SELECT * FROM team ORDER BY id DESC');
          $members = $teamStmt->fetchAll(PDO::FETCH_ASSOC);
          if (!empty($members)):
            $delay = 100;
            foreach ($members as $m): ?>
              <div class="col-lg-4 col-md-6 member" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                <div class="member-img">
                  <?php if (!empty($m['image']) && is_file(__DIR__ . '/assets/img/team/' . $m['image'])): ?>
                    <img src="<?php echo asset('assets/img/team/' . $m['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($m['name']); ?>">
                    <?php else: ?>
                    <img src="<?php echo site_image('team_default'); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($m['name']); ?>">
                  <?php endif; ?>
                  <div class="social">
                    <?php if (!empty($m['twitter'])): ?><a href="<?php echo htmlspecialchars($m['twitter']); ?>"><i class="bi bi-twitter"></i></a><?php endif; ?>
                    <?php if (!empty($m['facebook'])): ?><a href="<?php echo htmlspecialchars($m['facebook']); ?>"><i class="bi bi-facebook"></i></a><?php endif; ?>
                    <?php if (!empty($m['instagram'])): ?><a href="<?php echo htmlspecialchars($m['instagram']); ?>"><i class="bi bi-instagram"></i></a><?php endif; ?>
                    <?php if (!empty($m['linkedin'])): ?><a href="<?php echo htmlspecialchars($m['linkedin']); ?>"><i class="bi bi-linkedin"></i></a><?php endif; ?>
                  </div>
                </div>
                <div class="member-info text-center">
                  <h4><?php echo htmlspecialchars($m['name']); ?></h4>
                  <span><?php echo htmlspecialchars($m['role']); ?></span>
                  <p><?php echo htmlspecialchars($m['bio']); ?></p>
                </div>
              </div>
            <?php
              $delay += 100;
            endforeach;
          else: ?>
            <div class="col-12">
              <p>No team members available.</p>
            </div>
          <?php endif; ?>
        </div>

      </div>
    </section><!-- End Our Team Section -->

    <!-- ======= Testimonials Section ======= -->
    <section id="testimonials" class="testimonials section-bg">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>What Our Clients Say</h2>
          <p>Organizations, startups, and innovators trust Adatech to deliver reliable, scalable, and future-ready technology solutions.</p>
        </div>

        <div class="slides-2 swiper">
          <div class="swiper-wrapper">

            <?php
            // Load testimonials from DB
            $tstmt = $pdo->query('SELECT * FROM testimonials WHERE visible = 1 ORDER BY id DESC');
            $testimonials = $tstmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($testimonials)):
              foreach ($testimonials as $t):
                if (!empty($t['image'])) {
                  if (preg_match('#^https?://#i', $t['image'])) {
                    $img = $t['image'];
                  } elseif (strpos($t['image'], '/') !== false) {
                    $img = asset(ltrim($t['image'], '/\\'));
                  } else {
                    $img = asset('assets/img/testimonials/' . $t['image']);
                  }
                } else {
                  $img = site_image('team_default');
                }
                $author = $t['author'] ?? $t['name'] ?? 'Anonymous';
                $company = $t['company'] ?? $t['role'] ?? '';
                $quote = $t['quote'] ?? $t['message'] ?? '';
            ?>
                <div class="swiper-slide">
                  <div class="testimonial-wrap">
                    <div class="testimonial-item">
                      <img src="<?= htmlspecialchars($img) ?>" class="testimonial-img admin-inserted" alt="<?= htmlspecialchars($author) ?>">
                      <h3><?= htmlspecialchars($author) ?></h3>
                      <h4><?= htmlspecialchars($company) ?></h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                      <p>
                        <i class="bi bi-quote quote-icon-left"></i>
                        <?= nl2br(htmlspecialchars($quote)) ?>
                        <i class="bi bi-quote quote-icon-right"></i>
                      </p>
                    </div>
                  </div>
                </div>
              <?php
              endforeach;
            else:
              ?>
              <div class="swiper-slide">
                <div class="testimonial-wrap">
                  <div class="testimonial-item">
                    <p>No testimonials available.</p>
                  </div>
                </div>
              </div>
            <?php endif; ?>

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>
    </section>
    <!-- End Testimonials Section -->

    <!-- ======= Feedback Section (admin-managed) ======= -->
    <section id="feedback" class="testimonials section-bg">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Client Feedback</h2>
          <p>What clients and partners have told us through our feedback form.</p>
        </div>

        <div class="row">
          <?php
          try {
            require_once __DIR__ . '/includes/db.php';
            $fs = $pdo->query('SELECT * FROM feedback WHERE visible = 1 ORDER BY created_at DESC LIMIT 8')->fetchAll(PDO::FETCH_ASSOC);
          } catch (Exception $e) {
            $fs = [];
          }
          if (!empty($fs)):
            foreach ($fs as $f):
              $author = htmlspecialchars($f['name'] ?? 'Anonymous');
              $company = htmlspecialchars($f['company'] ?? '');
              $msg = nl2br(htmlspecialchars($f['message']));
          ?>
              <div class="col-lg-6" data-aos="fade-up">
                <div class="testimonial-wrap">
                  <div class="testimonial-item">
                    <h3><?php echo $author; ?></h3>
                    <h4><?php echo $company; ?></h4>
                    <p><?php echo $msg; ?></p>
                  </div>
                </div>
              </div>
            <?php
            endforeach;
          else:
            ?>
            <div class="col-12">
              <p>No client feedback available yet.</p>
            </div>
          <?php endif; ?>
        </div>

      </div>
    </section>
    <!-- End Feedback Section -->


  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">

    <div class="footer-content position-relative">
      <div class="container">
        <div class="row">

          <div class="col-lg-4 col-md-6">
            <div class="footer-info">
              <h3>Adatech</h3>
              <p>
                Addis Ababa, Ethiopia<br><br>
                <strong>Phone:</strong> +251986988762<br>
                <strong>Email:</strong> info@adatechglobal.com<br>
                <br>
                <strong>Website:</strong> <a href="http://www.adatechglobal.com">www.adatechglobal.com</a>
              </p>
              <div class="social-links d-flex mt-3">
                <a href="#" class="d-flex align-items-center justify-content-center"><i class="bi bi-twitter"></i></a>
                <a href="#" class="d-flex align-items-center justify-content-center"><i class="bi bi-facebook"></i></a>
                <a href="#" class="d-flex align-items-center justify-content-center"><i class="bi bi-instagram"></i></a>
                <a href="#" class="d-flex align-items-center justify-content-center"><i class="bi bi-linkedin"></i></a>
              </div>
            </div>
          </div><!-- End footer info column-->

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Useful Links</h4>
            <ul>
              <li><a href="#">Home</a></li>
              <li><a href="#">About us</a></li>
              <li><a href="#">Services</a></li>
              <li><a href="#">Terms of service</a></li>
              <li><a href="#">Privacy policy</a></li>
            </ul>
          </div><!-- End footer links column-->

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Our Services</h4>
            <ul>
              <li><a href="#">Web Design</a></li>
              <li><a href="#">Web Development</a></li>
              <li><a href="#">Product Management</a></li>
              <li><a href="#">Marketing</a></li>
              <li><a href="#">Graphic Design</a></li>
            </ul>
          </div><!-- End footer links column-->

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Hic solutasetp</h4>
            <ul>
              <li><a href="#">Molestiae accusamus iure</a></li>
              <li><a href="#">Excepturi dignissimos</a></li>
              <li><a href="#">Suscipit distinctio</a></li>
              <li><a href="#">Dilecta</a></li>
              <li><a href="#">Sit quas consectetur</a></li>
            </ul>
          </div><!-- End footer links column-->

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Nobis illum</h4>
            <ul>
              <li><a href="#">Ipsam</a></li>
              <li><a href="#">Laudantium dolorum</a></li>
              <li><a href="#">Dinera</a></li>
              <li><a href="#">Trodelas</a></li>
              <li><a href="#">Flexo</a></li>
            </ul>
          </div><!-- End footer links column-->

        </div>
      </div>
    </div>

    <div class="footer-legal text-center position-relative">
      <div class="container">
        <div class="copyright">
          &copy; Copyright <strong><span>Adatech</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
          <!-- All the links in the footer should remain intact. -->
          <!-- You can delete the links only if you purchased the pro version. -->
          <!-- Licensing information: https://bootstrapmade.com/license/ -->
          <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/upconstruction-bootstrap-construction-website-template/ -->
          Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a
            href="https://themewagon.com">ThemeWagon</a>
        </div>
      </div>
    </div>

  </footer>
  <!-- End Footer -->

  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>