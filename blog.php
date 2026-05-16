<?php
include("admin/inc/config.php");
session_start();
$user_id = "";
if (isset($_SESSION['user_id']))
  $user_id = $_SESSION['user_id'];
else if (isset($_SESSION['temp_user_id']))
  $user_id = $_SESSION['temp_user_id'];

include('include/header.php');

?>


<style>

      .custom-breadcrumb-bg {
      background-image: url('assets/images/blog_banner.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      height: 70vh; /* Fixed height for all devices */
      display: flex;
      align-items: center ;
    }

    @media (max-width: 768px) {
      .custom-breadcrumb-bg {
        height: 300px; /* Same height on mobile */
      }
    }

    .breadcrumb-contain h2{
font-size: 30px !important;
    }
    .breadcrumb-contain {
      color: #fff; /* Optional: Make text white for better visibility */
    }

</style>
 
   <!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0 custom-breadcrumb-bg">
  

  <div class="container-fluid-lg">
    <div class="row">
      <div class="col-12">
        <div class="breadcrumb-contain">
            <h2 style="font-size: 50px !important; color: #fff;">Blog</h2>
          
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Breadcrumb Section End -->




<div class="container py-5">
  <h4 class="mb-4">Latest Blogs</h4>
  <div class="row">
    <!-- Left: Big Blog -->
    <div class="col-lg-8 big-blog">
      <div class="blog-card p-3 d-flex flex-column flex-md-row align-items-start">
        <img src="https://images.unsplash.com/photo-1547931587-2841fef9d915?w=400&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGFzdHJvbG9nZXIlMjBtYWdpY2FsJTIwYm9va3xlbnwwfHwwfHx8MA%3D%3D" class="blog-img mb-3 mb-md-0 me-md-3" alt="Blog Image">
        <div>
          <p class="blog-meta mb-1">Ira Jain - Jul 03, 2025</p>
          <h5>Pyrite Stone Buying Guide: How To Choose The Right Pyrite Stone Online</h5>
          <p class="text-muted mb-0">Ready to Turn Possibility into Prosperity? Unlock the energy of authentic Pyrite stone, nature’s ...</p>
        </div>
      </div>
      <p class="container-fluid" style="font-size: 16px; line-height: 1.5; margin-top: 40px;">Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus, velit officiis temporibus ipsum autem sit, nihil ipsam odio doloribus quas, sint vero. Sapiente aperiam neque exercitationem expedita explicabo! Esse, rerum!
      Provident harum sequi officiis aut magni corrupti commodi animi similique nisi debitis vel enim consequuntur aliquam quas tempore a excepturi, doloribus nostrum. Repudiandae aliquam aspernatur eos temporibus, commodi neque pariatur?
      Incidunt, reprehenderit repellendus consequatur vitae quos illo maxime et nam qui placeat odio ipsum praesentium excepturi omnis tenetur aliquam ut unde magnam dignissimos iste est explicabo. Quisquam illo debitis mollitia.</p>
    </div>

    <!-- Right: Small Blogs -->
    <div class="col-lg-4">
      <!-- Card 1 -->
      <div class="blog-card d-flex mb-3 p-2 align-items-start">
        <img src="https://images.unsplash.com/photo-1594998100254-3c65bef09e53?w=400&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fGFzdHJvbG9nZXIlMjBtYWdpY2FsJTIwYm9va3xlbnwwfHwwfHx8MA%3D%3D" class="small-blog-img me-3" alt="Blog">
        <div>
          <p class="blog-meta mb-1">Sarthak Syal - 03 July 2025</p>
          <h6 class="mb-1">Which Rashi Can Wear Pyrite Stone?</h6>
          <p class="text-muted small mb-0">Leo, Aries, Scorpio, and Capricorn, your bold energy was ...</p>
        </div>
        <p>lore</p>
      </div>

      <!-- Card 2 -->
      <div class="blog-card d-flex mb-3 p-2 align-items-start">
        <img src="https://images.unsplash.com/photo-1606337740587-3aee763fec8b?w=400&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTl8fGFzdHJvbG9nZXIlMjBtYWdpY2FsJTIwYm9va3xlbnwwfHwwfHx8MA%3D%3D" class="small-blog-img me-3" alt="Blog">
        <div>
          <p class="blog-meta mb-1">Ira Jain - 02 July 2025</p>
          <h6 class="mb-1">Money Magnet Bracelet: The Secret Weapon Every Entrepreneur Should Wear</h6>
          <p class="text-muted small mb-0">Struggling to break through invisible financial barriers? ...</p>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="blog-card d-flex mb-3 p-2 align-items-start">
        <img src="https://plus.unsplash.com/premium_photo-1700081739302-4e8d766e1b9d?w=400&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mzd8fGFzdHJvbG9nZXIlMjBtYWdpY2FsJTIwYm9va3xlbnwwfHwwfHx8MA%3D%3D" class="small-blog-img me-3" alt="Blog">
        <div>
          <p class="blog-meta mb-1">Sarthak Syal - 02 July 2025</p>
          <h6 class="mb-1">Pyrite Bracelet For Money: Can This Stone Really Attract Abundance?</h6>
          <p class="text-muted small mb-0">Golden, gleaming, and full of possibility, our Pyrite Bra...</p>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Blog Section Start -->
    <section class="blog-section">
        <div class="container-fluid-lg">
            <div class="title title-4">
                <h3> Rudraksha</h3>
            </div>

            <div class="slider-3-blog arrow-slider slick-height">
                <div>
                    <div class=" blog-box ratio_45">
                        <div class="blog-box-image">
                            <a href="blog-detail.php">
                                <img src="https://astrotalk.store/cdn/shop/articles/Your_paragraph_text_8_9e0c01bb-46cb-4891-8620-daf70119377a_720x480.jpg?v=1751031255" class="blur-up lazyload bg-img" alt="">
                            </a>
                        </div>

                        <div class="blog-detail">
                            <label>Conversion rate optimization</label>
                            <a href="blog-detail.php" style="text-decoration: none;">
                                <h4>Karungali Mala Uses: Benefits, Powers & the Deity It Is Associated With</h4>
                            </a>
                            <h5 class="text-content" style="color: red;">Anil Viradiya - MARCH 9, 2022</h5>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="blog-box ratio_45">
                        <div class="blog-box-image">
                            <a href="blog-detail.php">
                                <img src="https://astrotalk.store/cdn/shop/articles/11_Mukhi_Rudraksha_Benefits_and_Wearing_Rules_for_Maximum_Impact.jpg?v=1750940869&width=450" class="blur-up lazyload bg-img" alt="">
                            </a>
                        </div>

                        <div class="blog-detail">
                            <label>Email Marketing</label>
                            <a href="blog-detail.php" style="text-decoration: none !important;">
                                <h4>11 Mukhi Rudraksha Benefits and Wearing Rules for Maximum Impact</h4>
                            </a>
                            <h5 class="text-content" style="color: red;">Anil Viradiya - MARCH 9, 2022</h5>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="blog-box ratio_45">
                        <div class="blog-box-image">
                            <a href="blog-detail.php">
                                <img src="https://astrotalk.store/cdn/shop/articles/Your_paragraph_text_10_bb2375ea-4edc-444d-838e-41dd0cb852b8.jpg?v=1751031662&width=750" class="blur-up lazyload bg-img" alt="">
                            </a>
                        </div>

                        <div class="blog-detail">
                            <label>Conversion rate optimization</label>
                            <a href="blog-detail.php">
                                <h3>Best selling bag online market place...</h3>
                            </a>
                            <h5 class="text-content">Anil Viradiya - MARCH 9, 2022</h5>
                        </div>
                        
                    </div>
                </div>

                <div>
                    <div class="blog-box ratio_45">
                        <div class="blog-box-image">
                            <a href="blog-detail.php">
                                <img src="https://astrotalk.store/cdn/shop/articles/11_Mukhi_Rudraksha_Benefits_and_Wearing_Rules_for_Maximum_Impact.jpg?v=1750940869&width=750" class="blur-up lazyload bg-img" alt="">
                            </a>
                        </div>

                        <div class="blog-detail">
                            <label>Conversion rate optimization</label>
                            <a href="blog-detail.php">
                                <h3>Best selling bag online market place...</h3>
                            </a>
                            <h5 class="text-content">Anil Viradiya - MARCH 9, 2022</h5>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="blog-box ratio_45">
                        <div class="blog-box-image">
                            <a href="blog-detail.php">
                                <img src="https://astrotalk.store/cdn/shop/articles/Benefits_Of_Keeping_Hanuman_Ji_Murti.png?v=1748607631&width=750" class="blur-up lazyload bg-img" alt="">
                            </a>
                        </div>

                        <div class="blog-detail">
                            <label>Conversion rate optimization</label>
                            <a href="blog-detail.php">
                                <h3>Best selling bag online market place...</h3>
                            </a>
                            <h5 class="text-content">Anil Viradiya - MARCH 9, 2022</h5>
                        </div>
                        
                    </div>
                </div>


            </div>
        </div>
    </section>

        <section class="blog-section">
        <div class="container-fluid-lg">
            <div class="title title-4">
                <h3>Idol</h3>
            </div>

            <div class="slider-3-blog  slick-height">
                <div>
                    <div class=" blog-box ratio_45">
                        <div class="blog-box-image">
                            <a href="blog-detail.php">
                                <img src="https://astrotalk.store/cdn/shop/articles/Untitled_design_2_b9a5f37f-6d9d-4a28-b969-d21602942344.png?v=1749028460&width=750" class="blur-up lazyload bg-img" alt="">
                            </a>
                        </div>

                        <div class="blog-detail">
                            <label>Conversion rate optimization</label>
                            <a href="blog-detail.php" style="text-decoration: none;">
                                <h4>Karungali Mala Uses: Benefits, Powers & the Deity It Is Associated With</h4>
                            </a>
                            <h5 class="text-content" style="color: red;">Anil Viradiya - MARCH 9, 2022</h5>
                        </div>
                    </div>
                </div>
<div>
                    <div class=" blog-box ratio_45">
                        <div class="blog-box-image">
                            <a href="blog-detail.php">
                                <img src="https://astrotalk.store/cdn/shop/articles/Benefits_Of_Keeping_Hanuman_Ji_Murti.png?v=1748607631&width=750" class="blur-up lazyload bg-img" alt="">
                            </a>
                        </div>

                        <div class="blog-detail">
                            <label>Conversion rate optimization</label>
                            <a href="blog-detail.php" style="text-decoration: none;">
                                <h4>Karungali Mala Uses: Benefits, Powers & the Deity It Is Associated With</h4>
                            </a>
                            <h5 class="text-content" style="color: red;">Anil Viradiya - MARCH 9, 2022</h5>
                        </div>
                    </div>
                </div>
          
                
              
        </div>
    </section>
    <!-- Blog Section End -->

<?php
include ('include/footer.php');

?>