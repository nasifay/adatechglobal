<?php require_once __DIR__ . '/includes/helpers.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Adatech Solutions | Software, Embedded Systems & IoT â€” Ethiopia</title>
  <meta content="We deliver innovative software, embedded systems, and digital solutions that empower businesses, communities, and innovators to thrive in the digital era." name="description">
  <meta content="Adatech, Ada Technology, software development Ethiopia, IoT Ethiopia, smart devices, embedded systems, website design Ethiopia, mobile apps" name="keywords">

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
        
        <h1>Adatech<span>.</span></h1>
      </a>

      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="about.php">About us</a></li>
          <li><a href="services.php">Services</a></li>
          <li><a href="projects.php">Projects</a></li>
          <li><a href="team.php">Team</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="hero">

    <?php
    // Load landing content from DB if available
    require_once __DIR__ . '/includes/db.php';
    require_once __DIR__ . '/includes/helpers.php';
    require_once __DIR__ . '/includes/site_images.php';
    $landingStmt = $pdo->prepare('SELECT title, body FROM content WHERE type = ? LIMIT 1');
    $landingStmt->execute(['landing']);
    $landing = $landingStmt->fetch(PDO::FETCH_ASSOC);
    ?>

    <div class="info d-flex align-items-center">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6 text-center">
            <?php if ($landing): ?>
              <h2 data-aos="fade-down"><?php echo htmlspecialchars($landing['title']); ?></h2>

              <p data-aos="fade-up">We empower businesses, communities and innovators with web &amp; mobile apps, IoT and embedded systems, and cloud-hosted platforms tailored for the Ethiopian market.</p>
              <a data-aos="fade-up" data-aos-delay="200" href="contact.php" class="btn-get-started">Get Started</a>
              <a data-aos="fade-up" data-aos-delay="300" href="services.php" class="btn-get-started" style="margin-left:10px;">Explore Our Services</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div id="hero-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">

      <div class="carousel-item active" style="background-image: url('<?php echo site_image('hero_carousel_1'); ?>')">
      </div>
      <div class="carousel-item" style="background-image: url('<?php echo site_image('hero_carousel_2'); ?>')"></div>
      <div class="carousel-item" style="background-image: url('<?php echo site_image('hero_carousel_3'); ?>')"></div>
      <div class="carousel-item" style="background-image: url('<?php echo site_image('hero_carousel_4'); ?>')"></div>
      <div class="carousel-item" style="background-image: url('<?php echo site_image('hero_carousel_5'); ?>')"></div>

      <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
      </a>

      <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
      </a>

    </div>

  </section><!-- End Hero Section -->

  <main id="main">

    <!-- ======= Get Started Section ======= -->
    <section id="get-started" class="get-started section-bg">
      <div class="container">

        <div class="row justify-content-between gy-4">

          <div class="col-lg-6 d-flex align-items-center" data-aos="fade-up">
            <div class="content">
              <h3>Letâ€™s build something innovative together.</h3>
              <p>Contact Adatech for tailored software and hardware solutions. Tell us about your idea and weâ€™ll help turn it into a working product or platform.</p>
            </div>
          </div>

          <div class="col-lg-5" data-aos="fade">
            <form action="forms/quote.php" method="post" class="php-email-form">
              <h3>Get a quote</h3>
              <p>Request a quote for a website, app, IoT system, prototype or embedded solution â€” our team will follow up quickly.</p>
              <div class="row gy-3">

                <div class="col-md-12">
                  <input type="text" name="name" class="form-control" placeholder="Name" required>
                </div>

                <div class="col-md-12 ">
                  <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>

                <div class="col-md-12">
                  <input type="text" class="form-control" name="phone" placeholder="Phone" required>
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="6" placeholder="Message" required></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Your quote request has been sent successfully. Thank you!</div>

                  <button type="submit">Get a quote</button>
                </div>

              </div>
            </form>
          </div><!-- End Quote Form -->

        </div>

      </div>
    </section><!-- End Get Started Section -->

    <!-- ======= Solutions Section ======= -->
    <section id="constructions" class="constructions">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Our Solutions</h2>
          <p>ADA Technology Solutions builds software and hardware products: IoT systems, embedded controllers, web platforms and mobile apps designed for Ethiopian businesses and industry.</p>
        </div>

        <div class="row gy-4">

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="card-item">
              <div class="row">
                <div class="col-xl-5">
                  <div class="card-bg" style="background-image: url('<?php echo site_image('project_card_1'); ?>');"></div>
                </div>
                <div class="col-xl-7 d-flex align-items-center">
                  <div class="card-body">
                    <h4 class="card-title">IoT Sensor Platforms</h4>
                    <p>Low-power sensor networks, remote telemetry, dashboards and analytics â€” deploy scalable IoT solutions to monitor assets and automate decisions.</p>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- End Card Item -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="card-item">
              <div class="row">
                <div class="col-xl-5">
                  <div class="card-bg" style="background-image: url('<?php echo site_image('project_card_1'); ?>');"></div>
                </div>
                <div class="col-xl-7 d-flex align-items-center">
                  <div class="card-body">
                    <h4 class="card-title">Embedded Systems & Prototyping</h4>
                    <p>Hardware prototyping, PCB design and embedded firmware to turn concepts into working smart devices and controllers.</p>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- End Card Item -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <div class="card-item">
              <div class="row">
                <div class="col-xl-5">
                  <div class="card-bg" style="background-image: url('<?php echo site_image('project_card_1'); ?>');"></div>
                </div>
                <div class="col-xl-7 d-flex align-items-center">
                  <div class="card-body">
                    <h4 class="card-title">Web Platforms & APIs</h4>
                    <p>Robust web platforms, backend APIs and SaaS solutions for operations, reporting and customer-facing applications.</p>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- End Card Item -->

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
            <div class="card-item">
              <div class="row">
                <div class="col-xl-5">
                  <div class="card-bg" style="background-image: url('<?php echo site_image('project_card_1'); ?>');"></div>
                </div>
                <div class="col-xl-7 d-flex align-items-center">
                  <div class="card-body">
                    <h4 class="card-title">Mobile Apps & UX</h4>
                    <p>Native and cross-platform mobile applications with intuitive UX, secure authentication and cloud sync.</p>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- End Card Item -->

        </div>

      </div>
    </section><!-- End Solutions Section -->

    <!-- ======= Services Section ======= -->
    <section id="services" class="services section-bg">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Our Services</h2>
          <p>Adatech provides innovative software and hardware solutions that empower businesses, communities, and industries through integrated, future-ready technology.</p>
        </div>

        <div class="row gy-4">
          <?php
          // Load services from DB (managed from admin/manage_services.php)
          $servicesStmt = $pdo->query('SELECT * FROM services ORDER BY id ASC');
          $services = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);
          if (!empty($services)):
            $delay = 100;
            foreach ($services as $s):
              $iconHtml = '';
              if (!empty($s['icon'])) {
                // if icon looks like an asset path, render image; otherwise, ignore
                $iconPath = htmlspecialchars($s['icon']);
                $iconHtml = '<div class="icon"><img src="' . $iconPath . '" style="height:36px;" alt=""></div>';
              }
          ?>
              <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                <div class="service-item position-relative">
                  <?php echo $iconHtml; ?>
                  <h3><?php echo htmlspecialchars($s['title']); ?></h3>
                  <p><?php echo htmlspecialchars($s['description']); ?></p>
                  <a href="service-details.php?id=<?php echo (int)$s['id']; ?>" class="readmore stretched-link">Learn more <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
          <?php
              $delay += 100;
            endforeach;
          else:
          // no services registered
          endif;
          ?>
        </div>

      </div>
    </section> <!-- ======= Alt Services Section ======= -->
    <section id="alt-services" class="alt-services">
      <div class="container" data-aos="fade-up">

        <div class="row justify-content-around gy-4">
          <div class="col-lg-6 img-bg" style="background-image: url('<?php echo site_image('service_main'); ?>');" data-aos="zoom-in" data-aos-delay="100"></div>

          <div class="col-lg-5 d-flex flex-column justify-content-center">
            <h3>Innovation Driven by Ethiopian Ingenuity</h3>
            <p>Adatech builds technology that bridges software and hardware, solving real-world problems through context-driven innovation and modern engineering.</p>

            <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="100">
              <i class="bi bi-cpu flex-shrink-0"></i>
              <div>
                <h4><a class="stretched-link">Smart Hardware Solutions</a></h4>
                <p>From smart blanket and smart bed systems to full-scale IoT deployments, we design intelligent hardware tailored to user needs.</p>
              </div>
            </div>

            <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="200">
              <i class="bi bi-code-slash flex-shrink-0"></i>
              <div>
                <h4><a class="stretched-link">Custom Software Platforms</a></h4>
                <p>We develop scalable business platforms, mobile apps, enterprise systems, and cloud-based applications.</p>
              </div>
            </div>

            <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-lightbulb flex-shrink-0"></i>
              <div>
                <h4><a class="stretched-link">R&D & Product Development</a></h4>
                <p>We build prototypes, test new ideas, and support startups and enterprises in turning concepts into market-ready products.</p>
              </div>
            </div>

            <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-gear-wide-connected flex-shrink-0"></i>
              <div>
                <h4><a class="stretched-link">Systems Integration</a></h4>
                <p>We integrate software, hardware, and cloud systems into seamless solutions that enhance productivity and efficiency.</p>
              </div>
            </div>

          </div>
        </div>

      </div>
    </section> <!-- ======= Features Section ======= -->
    <section id="features" class="features section-bg">
      <div class="container" data-aos="fade-up">

        <ul class="nav nav-tabs row g-2 d-flex">
          <li class="nav-item col-3">
            <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#tab-1">
              <h4>Smart Hardware</h4>
            </a>
          </li>
          <li class="nav-item col-3">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-2">
              <h4>Software Solutions</h4>
            </a>
          </li>
          <li class="nav-item col-3">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-3">
              <h4>IoT & Automation</h4>
            </a>
          </li>
          <li class="nav-item col-3">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-4">
              <h4>Innovation Lab</h4>
            </a>
          </li>
        </ul>

        <div class="tab-content">

          <!-- TAB 1 -->
          <div class="tab-pane active show" id="tab-1">
            <div class="row">
              <div class="col-lg-6 d-flex flex-column justify-content-center">
                <h3>Advanced Smart Hardware Products</h3>
                <p class="fst-italic">Adatech creates hardware solutions designed to transform daily life and industry operations.</p>
                <ul>
                  <li><i class="bi bi-check2-all"></i> Smart blanket and smart bed systems with sensors and automation.</li>
                  <li><i class="bi bi-check2-all"></i> Embedded devices and custom electronics.</li>
                  <li><i class="bi bi-check2-all"></i> Industrial and consumer IoT hardware.</li>
                </ul>
              </div>
              <div class="col-lg-6 text-center">
                <img src="<?php echo site_image('innovation_puzzle'); ?>" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <!-- TAB 2 -->
          <div class="tab-pane" id="tab-2">
            <div class="row">
              <div class="col-lg-6 d-flex flex-column justify-content-center">
                <h3>Powerful, Scalable Software Platforms</h3>
                <p class="fst-italic">We build digital solutions that empower businesses and communities.</p>
                <ul>
                  <li><i class="bi bi-check2-all"></i> Web applications and mobile apps.</li>
                  <li><i class="bi bi-check2-all"></i> Enterprise software and management systems.</li>
                  <li><i class="bi bi-check2-all"></i> Cloud-based platforms and integrated services.</li>
                </ul>
              </div>
              <div class="col-lg-6 text-center">
                <img src="<?php echo site_image('efficiency_puzzle'); ?>" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <!-- TAB 3 -->
          <div class="tab-pane" id="tab-3">
            <div class="row">
              <div class="col-lg-6 d-flex flex-column justify-content-center">
                <h3>IoT, AI & Automation Solutions</h3>
                <ul>
                  <li><i class="bi bi-check2-all"></i> Smart monitoring and sensor systems.</li>
                  <li><i class="bi bi-check2-all"></i> Real-time data analytics and dashboards.</li>
                  <li><i class="bi bi-check2-all"></i> Intelligent automation for businesses.</li>
                </ul>
                <p class="fst-italic">We connect devices, data, and intelligence to create seamless automated experiences.</p>
              </div>
              <div class="col-lg-6 text-center">
                <img src="<?php echo site_image('screens_computers'); ?>" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <!-- TAB 4 -->
          <div class="tab-pane" id="tab-4">
            <div class="row">
              <div class="col-lg-6 d-flex flex-column justify-content-center">
                <h3>Adatech Innovation Lab</h3>
                <p class="fst-italic">Where ideas become prototypes, and prototypes become products.</p>
                <ul>
                  <li><i class="bi bi-check2-all"></i> Product R&D and technical experimentation.</li>
                  <li><i class="bi bi-check2-all"></i> Startup collaboration and co-building.</li>
                  <li><i class="bi bi-check2-all"></i> Testing, validation, and pilot deployments.</li>
                </ul>
              </div>
              <div class="col-lg-6 text-center">
                <img src="<?php echo site_image('iot_theme'); ?>" alt="" class="img-fluid">
              </div>
            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- ======= Our Projects Section ======= -->
    <section id="projects" class="projects">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Our Projects</h2>
          <p>Explore the innovative solutions we have built across IoT, Embedded Systems, Web, Mobile, and smart hardware products.</p>
        </div>

        <div class="portfolio-isotope" data-portfolio-filter="*" data-portfolio-layout="masonry"
          data-portfolio-sort="original-order">

          <ul class="portfolio-flters" data-aos="fade-up" data-aos-delay="100">
            <li data-filter="*" class="filter-active">All</li>
            <li data-filter=".filter-iot">IoT</li>
            <li data-filter=".filter-embedded">Embedded</li>
            <li data-filter=".filter-web">Web</li>
            <li data-filter=".filter-mobile">Mobile</li>
            <li data-filter=".filter-hardware">Hardware</li>
          </ul>

          <div class="row gy-4 portfolio-container" data-aos="fade-up" data-aos-delay="200">

            <!-- IoT Project -->
            <div class="col-lg-4 col-md-6 portfolio-item filter-iot">
              <div class="portfolio-content h-100">
                <img src="<?php echo site_image('project_card_1'); ?>" class="img-fluid" alt="IoT Sensor Suite">
                <div class="portfolio-info">
                  <h4>IoT Sensor Suite</h4>
                  <p>Real-time monitoring system for smart environments.</p>
                  <a href="<?php echo site_image('project_card_1'); ?>" title="IoT Sensor Suite"
                    data-gallery="portfolio-gallery-iot" class="glightbox preview-link"><i
                      class="bi bi-zoom-in"></i></a>
                  <a href="project-details-iot.php" title="More Details" class="details-link"><i
                      class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div>

            <!-- Embedded Controller -->
            <div class="col-lg-4 col-md-6 portfolio-item filter-embedded">
              <div class="portfolio-content h-100">
                <img src="<?php echo site_image('project_card_1'); ?>" class="img-fluid" alt="Embedded Controller">
                <div class="portfolio-info">
                  <h4>Embedded Controller</h4>
                  <p>Custom microcontroller firmware and board integration.</p>
                  <a href="<?php echo site_image('project_card_1'); ?>" title="Embedded Controller"
                    data-gallery="portfolio-gallery-embedded" class="glightbox preview-link"><i
                      class="bi bi-zoom-in"></i></a>
                  <a href="project-details-embedded.php" title="More Details" class="details-link"><i
                      class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div>

            <!-- Web Platform -->
            <div class="col-lg-4 col-md-6 portfolio-item filter-web">
              <div class="portfolio-content h-100">
                <img src="<?php echo site_image('project_card_1'); ?>" class="img-fluid" alt="Web Platform">
                <div class="portfolio-info">
                  <h4>Web Platform</h4>
                  <p>Enterprise dashboard and automation portal.</p>
                  <a href="<?php echo site_image('project_card_1'); ?>" title="Web Platform"
                    data-gallery="portfolio-gallery-web" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="project-details-web.php" title="More Details" class="details-link"><i
                      class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div>

            <!-- Mobile Application -->
            <div class="col-lg-4 col-md-6 portfolio-item filter-mobile">
              <div class="portfolio-content h-100">
                <img src="<?php echo site_image('project_card_1'); ?>" class="img-fluid" alt="Mobile Application">
                <div class="portfolio-info">
                  <h4>Mobile Application</h4>
                  <p>Intuitive and user-friendly cross-platform mobile apps.</p>
                  <a href="<?php echo site_image('project_card_1'); ?>" title="Mobile Application"
                    data-gallery="portfolio-gallery-mobile" class="glightbox preview-link"><i
                      class="bi bi-zoom-in"></i></a>
                  <a href="project-details-mobile.php" title="More Details" class="details-link"><i
                      class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div>

            <!-- Smart Blanket -->
            <div class="col-lg-4 col-md-6 portfolio-item filter-hardware">
              <div class="portfolio-content h-100">
                <img src="<?php echo site_image('project_card_1'); ?>" class="img-fluid" alt="Smart Blanket Innovation">
                <div class="portfolio-info">
                  <h4>Smart Blanket</h4>
                  <p>Temperature-controlled, health-tracking smart fabric system.</p>
                  <a href="<?php echo site_image('project_card_1'); ?>" title="Smart Blanket"
                    data-gallery="portfolio-gallery-hardware" class="glightbox preview-link"><i
                      class="bi bi-zoom-in"></i></a>
                  <a href="project-details-smart-blanket.php" title="More Details" class="details-link"><i
                      class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div>

            <!-- Smart Bed -->
            <div class="col-lg-4 col-md-6 portfolio-item filter-hardware">
              <div class="portfolio-content h-100">
                <img src="<?php echo site_image('project_card_1'); ?>" class="img-fluid" alt="Smart Bed System">
                <div class="portfolio-info">
                  <h4>Smart Bed System</h4>
                  <p>Sleep tracking, auto-adjustment, and IoT connectivity.</p>
                  <a href="<?php echo site_image('project_card_1'); ?>" title="Smart Bed"
                    data-gallery="portfolio-gallery-hardware" class="glightbox preview-link"><i
                      class="bi bi-zoom-in"></i></a>
                  <a href="project-details-smart-bed.php" title="More Details" class="details-link"><i
                      class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div>

          </div><!-- End Projects Container -->

        </div>

      </div>
    </section>
    <!-- End Our Projects Section -->


    <!-- ======= Testimonials Section ======= -->
    <section id="testimonials" class="testimonials section-bg">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Testimonials</h2>
          <p>What partners, clients, and innovators say about working with Adatech Global.</p>
        </div>

        <?php
        // Load testimonials from DB
        $tstmt = $pdo->query('SELECT * FROM testimonials WHERE visible = 1 ORDER BY id DESC');
        $testimonials = $tstmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="slides-2 swiper">
          <div class="swiper-wrapper">

            <?php if (!empty($testimonials)): ?>
              <?php foreach ($testimonials as $t):
                if (!empty($t['image'])) {
                  $imgRel = (strpos($t['image'], '/') !== false) ? ltrim($t['image'], '/\\') : 'assets/img/testimonials/' . $t['image'];
                } else {
                  $imgRel = 'assets/img/testimonials/default.jpg';
                }
                $author = $t['author'] ?? 'Anonymous';
                $company = $t['company'] ?? '';
                $quote = $t['quote'] ?? '';
              ?>
                <div class="swiper-slide">
                  <div class="testimonial-wrap">
                    <div class="testimonial-item">
                      <img src="<?php echo esc(asset($imgRel)); ?>" class="testimonial-img admin-inserted" alt="<?= esc($author) ?>">
                      <h3><?= esc($author) ?></h3>
                      <h4><?= esc($company) ?></h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                      <p>
                        <i class="bi bi-quote quote-icon-left"></i>
                        <?= nl2br(esc($quote)) ?>
                        <i class="bi bi-quote quote-icon-right"></i>
                      </p>
                    </div>
                  </div>
                </div><!-- End testimonial item -->
              <?php endforeach; ?>
            <?php else: ?>
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


    <!-- ======= Recent Blog Posts Section ======= -->
    <section id="recent-blog-posts" class="recent-blog-posts">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Recent Blog Posts</h2>
          <p>Insights, stories, and updates from Adatech Global â€” technology, innovation, and impact in Africa.</p>
        </div>

        <div class="row gy-5">

          <!-- Blog Post 1 -->
          <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="post-item position-relative h-100">

              <div class="post-img position-relative overflow-hidden">
                <img src="<?php echo site_image('screens_computers'); ?>" class="img-fluid" alt="">
                <span class="post-date">January 10</span>
              </div>

              <div class="post-content d-flex flex-column">

                <h3 class="post-title">How Digital Innovation Is Transforming African Startups</h3>

                <div class="meta d-flex align-items-center">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-person"></i> <span class="ps-2">Adatech Global Team</span>
                  </div>
                  <span class="px-3 text-black-50">/</span>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-folder2"></i> <span class="ps-2">Tech Innovation</span>
                  </div>
                </div>

                <hr>

                <a href="blog-details.php" class="readmore stretched-link">
                  <span>Read More</span><i class="bi bi-arrow-right"></i>
                </a>

              </div>

            </div>
          </div><!-- End post item -->

          <!-- Blog Post 2 -->
          <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="post-item position-relative h-100">

              <div class="post-img position-relative overflow-hidden">
                <img src="<?php echo site_image('screens_computers'); ?>" class="img-fluid" alt="">
                <span class="post-date">December 28</span>
              </div>

              <div class="post-content d-flex flex-column">

                <h3 class="post-title">Building Digital Skills: Empowering Youth for the Future</h3>

                <div class="meta d-flex align-items-center">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-person"></i> <span class="ps-2">Nasifay Girma</span>
                  </div>
                  <span class="px-3 text-black-50">/</span>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-folder2"></i> <span class="ps-2">Capacity Building</span>
                  </div>
                </div>

                <hr>

                <a href="blog-details.php" class="readmore stretched-link">
                  <span>Read More</span><i class="bi bi-arrow-right"></i>
                </a>

              </div>

            </div>
          </div><!-- End post item -->

          <!-- Blog Post 3 -->
          <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="post-item position-relative h-100">

              <div class="post-img position-relative overflow-hidden">
                <img src="<?php echo site_image('screens_computers'); ?>" class="img-fluid" alt="">
                <span class="post-date">November 19</span>
              </div>

              <div class="post-content d-flex flex-column">

                <h3 class="post-title">The Role of Tech in Sustainable Development Across Africa</h3>

                <div class="meta d-flex align-items-center">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-person"></i> <span class="ps-2">Adatech Editorial</span>
                  </div>
                  <span class="px-3 text-black-50">/</span>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-folder2"></i> <span class="ps-2">Sustainability</span>
                  </div>
                </div>

                <hr>

                <a href="blog-details.php" class="readmore stretched-link">
                  <span>Read More</span><i class="bi bi-arrow-right"></i>
                </a>

              </div>

            </div>
          </div><!-- End post item -->

        </div>

      </div>
    </section>
    <!-- End Recent Blog Posts Section -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">

      <div class="footer-content position-relative">
        <div class="container">
          <div class="row">

            <div class="col-lg-4 col-md-6">
              <div class="footer-info">
                <h3>Adatech Solutions</h3>
                <p>
                  Addis Ababa, Ethiopia<br><br>
                  <strong>Phone:</strong> +251986988762<br>
                  <strong>Email:</strong> info@adatechglobal.com<br>
                  <br>
                  <strong>Website:</strong> <a href="https://www.adatechglobal.com">www.adatechglobal.com</a>
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
              <h4>Useful Links</h4>
              <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About us</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="contact.php">Contact</a></li>
              </ul>
            </div><!-- End footer links column-->

            <div class="col-lg-2 col-md-3 footer-links">
              <h4>Our Services</h4>
              <ul>
                <li><a href="services.php#software">Software Development</a></li>
                <li><a href="services.php#embedded">Embedded Systems & IoT</a></li>
                <li><a href="services.php#cloud">Cloud & Hosting</a></li>
                <li><a href="services.php#consulting">Consulting & Support</a></li>
                <li><a href="services.php#mobile">Mobile Apps</a></li>
              </ul>
            </div><!-- End footer links column-->

            <div class="col-lg-2 col-md-3 footer-links">
              <h4>Resources</h4>
              <ul>
                <li><a href="#">Case Studies</a></li>
                <li><a href="#">Documentation</a></li>
                <li><a href="#">API Reference</a></li>
                <li><a href="#">Careers</a></li>
                <li><a href="#">Contact Sales</a></li>
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