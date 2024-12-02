<?php
require_once 'global.php';
include_once 'header.php';
?>
<div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-12 d-flex justify-content-between mb-4">
                <div>
                    <h1 class="jobhead">1,075,855 ads Stuff for Sale</h1>
                    <button class="btn btn-sell-car mt-2">Save search alert</button>
                </div>
                <div class="d-flex align-items-end">
                    <select class="form-select" style="width: auto;">
                        <option>Most recent first</option>
                        <option>Price: Low to high</option>
                        <option>Price: High to low</option>
                        <option>Price: Nearest first</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
              <div class="d-flex justify-content-between align-items-center ">
                <div class="d-flex align-items-center mb-4">
                  <span class="me-2">VIEW AS</span>
                  <div
                    class="view-option icon-list"
                    data-cols="1"
                    title="List view"
                  >
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </div>
                  <div
                    class="view-option icon-grid-2"
                    data-cols="2"
                    title="Two column grid"
                  >
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </div>
                  <div
                    class="view-option icon-grid-3 active"
                    data-cols="3"
                    title="Three column grid"
                  >
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </div>
                  <!-- <div class="view-option icon-grid-4" data-cols="4" title="Four column grid">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </div>
                            <div class="view-option icon-grid-5" data-cols="5" title="Five column grid">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </div> -->
                </div>
              </div>
    
              <div class="container ">
                <div
                  id="product-grid"
                  class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"
                >
                  <!-- Product Card 1 -->
                   <a href="<?php echo $urlval?>detail.php">
                  <div class="col">
                    <div class="product-card">
                      <img
                        src="https://imagedelivery.net/ePR8PyKf84wPHx7_RYmEag/4b9160a8-9109-4cdc-860d-0f1ddbca6700/86"
                        class="card-img-top"
                        alt="Classic Maroon Kameez Shalwar"
                      />
                      <div class="discount-badge">-25%</div>
                      <div class="card-body ">
                        <div class="p-3">
                            <h5 class="card-title">Room for rent</h5>
                            <p class="card-text">
                              Private | Date available: 15 Oct 2024 | House | 3 Beds
                              <br />Bradford, West Yorkshire
                            </p>
                            <p class="text-muted">Manchester</p>
                            <span class="product-price">£330</span>
                            <span class="product-time">Just now</span>
                        </div>
                        <button class="btn quick-add-btn">Quick Add</button>
                      </div>
                    </div>
                  </div>
                </a>
                  <!-- Product Card 2 -->
                   <a href="<?php echo $urlval?>detail.php">
                  <div class="col">
                    <div class="product-card">
                      <img
                        src="https://imagedelivery.net/ePR8PyKf84wPHx7_RYmEag/4b9160a8-9109-4cdc-860d-0f1ddbca6700/86"
                        class="card-img-top"
                        alt="Classic Maroon Kameez Shalwar"
                      />
                      <div class="discount-badge">-25%</div>
                        <div class="card-body">
                        <div class="p-3">
                            <h5 class="card-title">Room for rent</h5>
                            <p class="card-text">
                              Private | Date available: 15 Oct 2024 | House | 3 Beds
                              <br />Bradford, West Yorkshire
                            </p>
                            <p class="text-muted">Manchester</p>
                            <span class="product-price">£330</span>
                            <span class="product-time">Just now</span>
                        </div>
                        <button class="btn quick-add-btn">Quick Add</button>
                      </div>
                    </div>
                  </div>
                </a>
                  <!-- Product Card 3 -->
                   <a href="<?php echo $urlval?>detail.php">
                  <div class="col">
                    <div class="product-card">
                      <img
                        src="https://imagedelivery.net/ePR8PyKf84wPHx7_RYmEag/4b9160a8-9109-4cdc-860d-0f1ddbca6700/86"
                        class="card-img-top"
                        alt="Classic Maroon Kameez Shalwar"
                      />
                      <div class="discount-badge">-25%</div>
                      <div class="card-body">
                        <div class="p-3">
                            <h5 class="card-title">Room for rent</h5>
                            <p class="card-text">
                              Private | Date available: 15 Oct 2024 | House | 3 Beds
                              <br />Bradford, West Yorkshire
                            </p>
                            <p class="text-muted">Manchester</p>
                            <span class="product-price">£330</span>
                            <span class="product-time">Just now</span>
                        </div>
                        <button class="btn quick-add-btn">Quick Add</button>
                      </div>
                    </div>
                  </div>
                </a>
                  <!-- Product Card 4 -->
                   <a href="<?php echo $urlval?>detail.php">
                  <div class="col">
                    <div class="product-card">
                      <img
                        src="https://imagedelivery.net/ePR8PyKf84wPHx7_RYmEag/4b9160a8-9109-4cdc-860d-0f1ddbca6700/86"
                        class="card-img-top"
                        alt="Classic Maroon Kameez Shalwar"
                      />
                      <div class="discount-badge">-25%</div>
                      <div class="card-body">
                        <div class="p-3">
                            <h5 class="card-title">Room for rent</h5>
                            <p class="card-text">
                              Private | Date available: 15 Oct 2024 | House | 3 Beds
                              <br />Bradford, West Yorkshire
                            </p>
                            <p class="text-muted">Manchester</p>
                            <span class="product-price">£330</span>
                            <span class="product-time">Just now</span>
                        </div>
                        <button class="btn quick-add-btn">Quick Add</button>
                      </div>
                    </div>
                  </div>
                </a>
                  <!-- Product Card 5 -->
                   <a href="<?php echo $urlval?>detail.php">
                  <div class="col">
                    <div class="product-card">
                      <img
                        src="https://imagedelivery.net/ePR8PyKf84wPHx7_RYmEag/4b9160a8-9109-4cdc-860d-0f1ddbca6700/86"
                        class="card-img-top"
                        alt="Classic Maroon Kameez Shalwar"
                      />
                      <div class="discount-badge">-25%</div>
                      <div class="card-body">
                        <div class="p-3">
                            <h5 class="card-title">Room for rent</h5>
                            <p class="card-text">
                              Private | Date available: 15 Oct 2024 | House | 3 Beds
                              <br />Bradford, West Yorkshire
                            </p>
                            <p class="text-muted">Manchester</p>
                            <span class="product-price">£330</span>
                            <span class="product-time">Just now</span>
                        </div>
                        <button class="btn quick-add-btn">Quick Add</button>
                      </div>
                    </div>
                  </div>
                </a>
                </div>
              </div>
            </div>
            <div class="col-md-3 p-3 mb-5 sale-loc left-side">
              <div class="mb-4 mt-3 p-3 sale-loc">
                  <h5>Location</h5>
                  <div class="input-group mb-3">
                      <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                      <input type="text" class="form-control" placeholder="United Kingdom">
                  </div>
                  <div class="mb-3">
                      <select class="form-select">
                          <option selected>Choose Distance</option>
                          <option value="50000">1 miles</option>
                          <option value="70000">3 miles</option>
                          <option value="100000">5 miles</option>
                          <option value="150000">10 miles</option>
                      </select>
                  </div>
                  <button class="btn btn-sell-car w-100">Update</button>
              </div>

              <div class="mb-2 p-3 sale-loc">
                  <h5>Category</h5>
                  <div class="ms-3">
                      <div class="custom-category">
                          <p>Home & Garden</p>
                      </div>
                      <div class="custom-category">
                          <p>Baby & Kids Stuff <span class="text-muted">(5637)</span></p>
                      </div>
                      <div class="custom-category">
                          <p>Clothes, Footwear & <br>Accessories <span class="text-muted">(4516)</span></p>
                      </div>
                      <div class="custom-category">
                          <p>Sports, Leisure & Travel <span class="text-muted">(4270)</span></p>
                      </div>
                      <div class="custom-category">
                          <p>Other Goods <span class="text-muted">(3147)</span></p>
                      </div>
                      <div class="custom-category">
                          <p>DIY Tools & <br>Materials <span class="text-muted">(3070)</span></p>
                      </div>
                  </div>
                  <div class="more-categories" id="moreCategories" style="display: none;">
                      <div class="ms-3 mt-2">
                          <div class="custom-category">
                              <p>Appliances <span class="text-muted">(2911)</span></p>
                          </div>
                          <div class="custom-category">
                              <p>Computers & Software <span class="text-muted">(2371)</span></p>
                          </div>
                          <div class="custom-category">
                              <p>Health & Beauty<span class="text-muted">(2365)</span></p>
                          </div>
                          <div class="custom-category">
                              <p>Office Furniture &<br> Equipment <span class="text-muted">(2231)</span></p>
                          </div>
                      </div>
                  </div>
                  <a href="<?php echo $urlval?>" class="show pt-1" id="toggleCategories">Show 4 more</a>
              </div>
              <div class="more-categories" id="moreCategories" style="display: none;">
                  <div class="ms-3 mt-2">
                      <div class="custom-category">
                          <p>Sales <span class="text-muted">(2911)</span></p>
                      </div>
                      <div class="custom-category">
                          <p>Agriculture & Farming <span class="text-muted">(2371)</span></p>
                      </div>
                  </div>
                      <div class="custom-category">
                          <p>Jobs</p>
                      </div>
                      <div class="custom-category">
                          <p>Teaching & Education <span class="text-muted">(5637)</span></p>
                      </div>
                      <div class="custom-category">
                          <p>Transport & Logistics <span class="text-muted">(4516)</span></p>
                      </div>
                      <div class="custom-category">
                          <p>Manufacturing & Industrial <span class="text-muted">(4270)</span></p>
                      </div>
                      <div class="custom-category">
                          <p>Retail & FMCG <span class="text-muted">(3147)</span></p>
                      </div>
                      <div class="custom-category">
                          <p>Engineering <span class="text-muted">(3070)</span></p>
                      </div>
                  </div>
                  <div class="more-categories" id="moreCategories" style="display: none;">
                      <div class="ms-3 mt-2">
                          <div class="custom-category">
                              <p>Sales <span class="text-muted">(2911)</span></p>
                          </div>
                          <div class="custom-category">
                              <p>Agriculture & Farming <span class="text-muted">(2371)</span></p>
                          </div>
                  </div>
                  <a href="<?php echo $urlval?>" class="show" id="toggleCategories">Show 30 more</a>
              </div>

              <div class=" p-3 sale-loc">
                  <h5>Price</h5>
                  <div class="mb-3">
                      <input type="number" class="form-control" placeholder="Min price">
                  </div>
                  <div class="mb-3">
                      <input type="number" class="form-control" placeholder="Max price">
                  </div>
                  <button class="btn btn-sell-car w-100">Update</button>
              </div>
          </div>
        </div>
    </div>
<?php
include_once 'header.php';
?>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="js/index.js"></script>
            <script src="js/header.js"></script>
            <script src="/js/forsale.js"></script>
</body>
</html>