<?php
require_once 'global.php';
include_once 'header.php';

?>
<header class="hero-section">
      <div class="container hero-content">
        <h1 class="text-center text-white mb-2"><b>Find Cars for Sale </b></h1>
        <p class="text-center text-white mb-4">
          Search thousands of ads on the UK's local motors marketplace*
        </p>
        <div class="search-form">
          <div class="row g-3">
            <div class="col-md-3 col-sm-6">
              <label for="make" class="form-label">Make</label>
              <select id="make" class="form-select">
                <option selected>Select make</option>
              </select>
            </div>
            <div class="col-md-3 col-sm-6">
              <label for="model" class="form-label">Model</label>
              <select id="model" class="form-select">
                <option selected>Select model</option>
              </select>
            </div>
            <div class="col-md-3 col-sm-6">
              <label for="min-price" class="form-label">Price range</label>
              <div class="d-flex">
                <select id="min-price" class="form-select me-2">
                  <option selected>No min</option>
                </select>
                <select id="max-price" class="form-select">
                  <option selected>No max</option>
                </select>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <label for="location" class="form-label">Location</label>
              <div class="location-input">
                <span class="location-icon">📍</span>
                <input
                  type="text"
                  class="form-control ps-4"
                  id="location"
                  placeholder="United Kingdom"
                />
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-9 col-sm-6 d-flex align-items-center">
              <a href="<?php echo $urlval?>" class="more-options">More options ▼</a>
            </div>
            <div class="col-md-3 col-sm-6 mt-2 mt-sm-0">
              <button type="submit" class="btn btn-search text-white w-100">
                Search Cars (224,232)
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <section class="sell-section">
      <div class="container">
        <h2 class="text-center mb-4">
        </h2>
        <div class="d-flex justify-content-center align-items-center mb-3">
          <span class="me-3"> Sell your car online for FREE </span>
          <span>Selling a car is free for private sellers</span>
        </div>
        <div class="d-flex justify-content-center">
          <button class="btn btn-enter-reg me-3">
            <span class="eu-flag"></span>
            ENTER REG
          </button>
          <button class="btn btn-sell-car">Sell my car</button>
        </div>
        <div class="newone text-center mt-3">
          <a href="<?php echo $urlval?>">Learn more about how to sell your car</a>
        </div>
      </div>
    </section>
    <section class="py-5">
      <div class="container ">
        <h2 class="text-center mb-5"><b>Popular Models</b></h2>
        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
              <div>
            <div class="carousel-item active " data-bs-interval="10000">
              <div class="row">
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="<?php echo $urlval?>custom/asset/car1.jpg"
                        class="card-img-top"
                        alt="Circular Economy"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Vauxhall Corsa</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="<?php echo $urlval?>/asset/car2.jpg"
                        class="card-img-top"
                        alt="Upcycle"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Volkswagen Polo</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="/asset/car8.jpg"
                        class="card-img-top"
                        alt="Not Local"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Audi A3</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="/asset/car5.jpg"
                        class="card-img-top"
                        alt="Sell unwanted"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Toyota Supra</p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="carousel-item " data-bs-interval="20000">
              <div class="row">
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="https://www.Gumtree.com/assets/frontend/gaming.9ce2c4f1fce1d7945df76e93395cc8ee.png"
                        class="card-img-top"
                        alt="Circular Economy"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Our Circular Economy R...</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="https://www.Gumtree.com/assets/frontend/latest_jobs_2.b05155d69e733cab888d04e884bbe94d.png"
                        class="card-img-top"
                        alt="Upcycle"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">12 Ways to Upcycle You...</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="https://www.Gumtree.com/assets/frontend/cosy_living.49e97ecfd0c72454fb20cad5ec63788c.png"
                        class="card-img-top"
                        alt="Not Local"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Not Local? No Problem...</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="https://www.Gumtree.com/assets/frontend/affordable_runarounds.adfdb0369a985d884565bb10ce40800e.png"
                        class="card-img-top"
                        alt="Sell unwanted"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Sell unwanted electrical...</p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="carousel-item">
              <div class="row">
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="https://www.Gumtree.com/assets/frontend/gaming.9ce2c4f1fce1d7945df76e93395cc8ee.png"
                        class="card-img-top"
                        alt="Circular Economy"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Our Circular Economy R...</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="https://www.Gumtree.com/assets/frontend/latest_jobs_2.b05155d69e733cab888d04e884bbe94d.png"
                        class="card-img-top"
                        alt="Upcycle"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">12 Ways to Upcycle You...</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="https://www.Gumtree.com/assets/frontend/cosy_living.49e97ecfd0c72454fb20cad5ec63788c.png"
                        class="card-img-top"
                        alt="Not Local"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Not Local? No Problem...</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card crt-timg-hm">
                      <img
                        src="https://www.Gumtree.com/assets/frontend/affordable_runarounds.adfdb0369a985d884565bb10ce40800e.png"
                        class="card-img-top"
                        alt="Sell unwanted"
                      />
                      <div class="card-body">
                        <p class="card-text  crt-txt-hm">Sell unwanted electrical...</p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>  
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="<?php echo $urlval?>carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="<?php echo $urlval?>carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
      </div>
        <div class="text-center mt-5">
          <button class="btn btn-button px-4 py-2">See all makes</button>
        </div>
      </div>
    </section>
    <div class="container">
      <section class="py-5">
        <div class="container">
          <h2 class="text-center mb-5"><b>Browse by Body Type </b></h2>
          <div class="row">
            <div class="col-6 col-sm-4 col-md-2 text-center mb-4">
              <div
                class="bg-white rounded-circle shadow d-flex align-items-center justify-content-center body-type-icon mb-2"
              >
                <i class="fa-solid fa-car"></i>
              </div>
              <span>Hatchback</span>
            </div>
            <div class="col-6 col-sm-4 col-md-2 text-center mb-4">
              <div
                class="bg-white rounded-circle shadow d-flex align-items-center justify-content-center body-type-icon mb-2"
              >
                <i class="fa-solid fa-car"></i>
              </div>
              <span>Saloon</span>
            </div>
            <div class="col-6 col-sm-4 col-md-2 text-center mb-4">
              <div
                class="bg-white rounded-circle shadow d-flex align-items-center justify-content-center body-type-icon mb-2"
              >
                <i class="fa-solid fa-car"></i>
              </div>
              <span>Estate</span>
            </div>
            <div class="col-6 col-sm-4 col-md-2 text-center mb-4">
              <div
                class="bg-white rounded-circle shadow d-flex align-items-center justify-content-center body-type-icon mb-2"
              >
                <i class="fa-solid fa-car"></i>
              </div>
              <span>MPV</span>
            </div>
            <div class="col-6 col-sm-4 col-md-2 text-center mb-4">
              <div
                class="bg-white rounded-circle shadow d-flex align-items-center justify-content-center body-type-icon mb-2"
              >
                <i class="fa-solid fa-car"></i>
              </div>
              <span>Coupe</span>
            </div>
            <div class="col-6 col-sm-4 col-md-2 text-center mb-4">
              <div
                class="bg-white rounded-circle shadow d-flex align-items-center justify-content-center body-type-icon mb-2"
              >
                <i class="fa-solid fa-car"></i>
              </div>
              <span>Convertible</span>
            </div>
          </div>
        </div>
      </section>

      <h2 class="text-center mb-4">
        <b>Read our latest expert car reviews and articles</b>
      </h2>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
        <div class="col">
          <div class="card h-100">
            <img src="/asset/read1.jpg" class="card-img-top" alt="Car Review" />
            <div class="card-body">
              <p class="text-muted mb-1">Car reviews</p>
              <h5 class="card-title">Browse car reviews</h5>
              <p class="card-text">
                With our expert team's ratings and commentary, you'll think
                you've just been on a test drive.
              </p>
            </div>
            <div class="card-footer bg-transparent border-0">
              <button class="btn btn-outline-primary w-100">
                Read full review
              </button>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card h-100">
            <img src="/asset/read2.jpg" class="card-img-top" alt="Best Of" />
            <div class="card-body">
              <p class="text-muted mb-1">Best of</p>
              <h5 class="card-title">Read our best-of lists</h5>
              <p class="card-text">
                Whether you're after comfort, style or value, take the stress
                out of researching with our hand-picked cars.
              </p>
            </div>
            <div class="card-footer bg-transparent border-0">
              <button class="btn btn-outline-primary w-100">
                Read full review
              </button>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card h-100">
            <img
              src="/asset/read3.jpg"
              class="card-img-top"
              alt="Safety Tips"
            />
            <div class="card-body">
              <p class="text-muted mb-1">Car guides & advice</p>
              <h5 class="card-title">Safety tips & advice</h5>
              <p class="card-text">
                Learn how to stay safe when buying and selling your vehicle.
              </p>
            </div>
            <div class="card-footer bg-transparent border-0">
              <button class="btn btn-outline-primary w-100">
                Read full review
              </button>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card h-100">
            <img src="/asset/read4.jpg" class="card-img-top" alt="Cars Hub" />
            <div class="card-body">
              <p class="text-muted mb-1">Car guides & advice</p>
              <h5 class="card-title">Visit our cars hub</h5>
              <p class="card-text">
                Find everything you need to decide on your ideal used car
                inside.
              </p>
            </div>
            <div class="card-footer bg-transparent border-0">
              <button class="btn btn-outline-primary w-100">
                Read full review
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="text-end mb-3">
        <a href="<?php echo $urlval?>" class="linkcolor"
          >Read more from our Motors Hub <i class="fas fa-chevron-right"></i
        ></a>
      </div>
    </div>

    <section class="brand-section">
      <div class="container">
        <h2 class="text-center mb-5"><b>Browse by Brands </b></h2>
        <div class="row" id="brandContainer"></div>
      </div>
    </section>

    <section class="pt-5 py-3 bg-light">
      <div class="container">
        <h2 class="text-center mb-4"><b>Recently listed </b></h2>
        <div class="row">
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
              <img
                src="/asset/list3.avif"
                class="card-img-top"
                alt="BMW 116D"
              />
              <div class="card-body">
                <h5 class="card-title">
                  BMW 116D BUSINESS EDITION SAT NAV (2018)
                </h5>
                <p class="card-text">
                  <small
                    >2018 | 136,000 miles | Private | Diesel | 1,496 cc</small
                  ><br />
                  <small>Heckmondwike, West Yorkshire</small>
                </p>
                <p class="text-success fw-bold">£7,350</p>
                <small class="text-muted">Just now</small>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
              <img
                src="/asset/list3.avif"
                class="card-img-top"
                alt="BMW 5 Series"
              />
              <div class="card-body" `>
                <h5 class="card-title">
                  BMW 5 SERIES, Saloon, 2012, Semi-Auto, 1995 (cc)
                </h5>
                <p class="card-text">
                  <small
                    >2012 | 120,292 miles | Private | Diesel | 1,995 cc</small
                  ><br />
                  <small>Sherwood, Nottinghamshire</small>
                </p>
                <p class="text-success fw-bold">£4,250</p>
                <small class="text-muted">Just now</small>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
              <img
                src="/asset/list3.avif"
                class="card-img-top"
                alt="Ford Kuga"
              />
              <div class="card-body">
                <h5 class="card-title">
                  Ford KUGA, Estate, 2010, Manual, 1997 (cc), 5 doors
                </h5>
                <p class="card-text">
                  <small
                    >2010 | 128,410 miles | Private | Diesel | 1,997 cc</small
                  ><br />
                  <small>Winchester, Hampshire</small>
                </p>
                <p class="text-success fw-bold">£3,250</p>
                <small class="text-muted">Just now</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="pb-5 bg-light">
      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
              <img
                src="/asset/list4.avif"
                class="card-img-top"
                alt="BMW 116D"
              />
              <div class="card-body">
                <h5 class="card-title">
                  BMW 116D BUSINESS EDITION SAT NAV (2018)
                </h5>
                <p class="card-text">
                  <small
                    >2018 | 136,000 miles | Private | Diesel | 1,496 cc</small
                  ><br />
                  <small>Heckmondwike, West Yorkshire</small>
                </p>
                <p class="text-success fw-bold">£7,350</p>
                <small class="text-muted">Just now</small>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
              <img
                src="/asset/list4.avif"
                class="card-img-top"
                alt="BMW 5 Series"
              />
              <div class="card-body">
                <h5 class="card-title">
                  BMW 5 SERIES, Saloon, 2012, Semi-Auto, 1995 (cc)
                </h5>
                <p class="card-text">
                  <small
                    >2012 | 120,292 miles | Private | Diesel | 1,995 cc</small
                  ><br />
                  <small>Sherwood, Nottinghamshire</small>
                </p>
                <p class="text-success fw-bold">£4,250</p>
                <small class="text-muted">Just now</small>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
              <img
                src="/asset/list4.avif"
                class="card-img-top"
                alt="Ford Kuga"
              />
              <div class="card-body">
                <h5 class="card-title">
                  Ford KUGA, Estate, 2010, Manual, 1997 (cc), 5 doors
                </h5>
                <p class="card-text">
                  <small
                    >2010 | 128,410 miles | Private | Diesel | 1,997 cc</small
                  ><br />
                  <small>Winchester, Hampshire</small>
                </p>
                <p class="text-success fw-bold">£3,250</p>
                <small class="text-muted">Just now</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div class="container">
      <div class="row top-section">
          <div class="col-md-12 pt-4">
            <span class="tp-lctn-mn d-flex justify-content-between">
              <h5 class="text-center mb-5 tp-lct-icn"><b>Top Searches</b></h5>
              <h5><b>Top Locations</b></h5>
            </span>
              <div class="search-links text-center pb-5 d-flex justify-content-evenly">
                  <a href="<?php echo $urlval?>" class="search-link">cars birmingham</a>
                  <a href="<?php echo $urlval?>" class="search-link">bournemouth cars</a>
                  <a href="<?php echo $urlval?>" class="search-link">cars convertables</a>
                  <a href="<?php echo $urlval?>" class="search-link">cars renfrewshire</a>
                  <a href="<?php echo $urlval?>" class="search-link">bedworth cars</a>
                  <a href="<?php echo $urlval?>" class="search-link">cars banbridge</a>
                  <a href="<?php echo $urlval?>" class="search-link">cars dorchester</a>
                  <a href="<?php echo $urlval?>" class="search-link">convertables cars</a>
              </div>
          </div>
      </div>
  </div>
  <?php 
  include_once 'footer.php';
  ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="index.js"></script>
    <script src="js/header.js"></script>
  </body>
    </html>