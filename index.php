<?php

include 'includes/header.php';
include 'config/db.php'; // isme ab $conn PDO instance return karega

try {
    // Fetch rooms from database
    $sql = "SELECT * FROM rooms WHERE status = 'Active' LIMIT 6";
    $stmt = $conn->query($sql);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch feedback from database
    $sql = "SELECT f.feedback_message, u.username 
            FROM feedback f 
            JOIN users u ON f.user_id = u.user_id 
            ORDER BY f.id DESC LIMIT 30";
    $stmt = $conn->query($sql);
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "❌ Query failed: " . $e->getMessage();
    $rooms = [];
    $feedbacks = [];
}

// Display login success/error messages
if (isset($_SESSION['login_success'])) {
    echo '<div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; background: #4CAF50; color: white; padding: 15px; border-radius: 5px; z-index: 1000; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">';
    echo htmlspecialchars($_SESSION['login_success']);
    echo '</div>';
    unset($_SESSION['login_success']);
}

if (isset($_SESSION['login_error'])) {
    echo '<div class="alert alert-error" style="position: fixed; top: 20px; right: 20px; background: #f44336; color: white; padding: 15px; border-radius: 5px; z-index: 1000; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">';
    echo htmlspecialchars($_SESSION['login_error']);
    echo '</div>';
    unset($_SESSION['login_error']);
}
?>
           
           
           <div class="hotale-page-wrapper" id="hotale-page-wrapper">
                <div class="gdlr-core-page-builder-body">
                    <div class="gdlr-core-pbf-wrapper" style="padding: 0px 0px 0px 0px;">
                        <div class="gdlr-core-pbf-background-wrap">
                            <div
                                class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js"
                                style="background-image: url(upload/apartment-2-hero-bg.png); background-repeat: repeat; background-position: top center;"
                                data-parallax-speed="0"
                            ></div>
                        </div>
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
                                <div class="gdlr-core-pbf-column gdlr-core-column-30 gdlr-core-column-first" id="gdlr-core-column-1">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="margin: 0px 0px 0px 0px; padding: 175px 80px 200px 0px;" data-sync-height="h1">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div
                                            class="gdlr-core-pbf-column-content clearfix gdlr-core-js gdlr-core-sync-height-content"
                                            data-gdlr-animation="fadeInLeft"
                                            data-gdlr-animation-duration="600ms"
                                            data-gdlr-animation-offset="0.8">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 25px;">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3
                                                            class="gdlr-core-title-item-title gdlr-core-skin-title class-test"
                                                            style="font-size: 72px; font-weight: 400; letter-spacing: 0px; line-height: 1; text-transform: none; color: #141414;"
                                                        >
                                                            Apartments for rent in <span style="font-weight: 700;">London</span><span style="color: #74c586; font-size: 72px;">.</span>
                                                            <span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-left-align" style="padding-bottom: 15px;">
                                                    <div class="gdlr-core-text-box-item-content" style="font-size: 18px; text-transform: none; color: #94959b;">
                                                        <p>Search millions of apartments, houses, and private office suites for rent with our exclusive hotels &amp; apartments app.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-divider-item gdlr-core-divider-item-normal gdlr-core-item-pdlr gdlr-core-left-align">
                                                    <div class="gdlr-core-divider-container" style="max-width: 40px;">
                                                        <div class="gdlr-core-divider-line gdlr-core-skin-divider" style="border-color: #74c586; border-width: 3px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-30" id="gdlr-core-column-2">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js gdlr-core-column-extend-right" style="padding: 400px 0px 0px 0px;" data-sync-height="h1">
                                        <div class="gdlr-core-pbf-background-wrap" style="background-color: #878787;">
                                            <div
                                                class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js"
                                                style="
                                                    background-image: url(uploads/andrew-neel-T0eb55DxDN4-unsplash.jpg);
                                                    background-size: cover;
                                                    background-position: center;
                                                "
                                                data-parallax-speed="0"
                                            ></div>
                                        </div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js gdlr-core-sync-height-content"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gdlr-core-pbf-wrapper" style="margin: -90px auto 0px auto; padding: 5px 0px 5px 0px; max-width: 920px;">
                        
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container-custom">
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" data-skin="Green Button" id="gdlr-core-column-3">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="margin: 0px 0px 0px 0px; padding: 20px 10px 0px 10px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="tourmaster-room-search-item tourmaster-item-pdlr clearfix">
                                                    <form
                                                        class="tourmaster-room-search-form tourmaster-radius-normal tourmaster-style-text-top tourmaster-align-horizontal"
                                                        action="#"
                                                        method="get"
                                                    >
                                                       
                                                        </div>
                                                        
                                                        
                                                        </div>
                                                       
                                                        <input type="hidden" name="room-search" value="" />
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gdlr-core-pbf-wrapper" style="padding: 140px 0px 30px 0px;" id="gdlr-core-wrapper-1">
                        <div class="gdlr-core-pbf-background-wrap"></div>
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
                                <div class="gdlr-core-pbf-column gdlr-core-column-30 gdlr-core-column-first" id="gdlr-core-column-4">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" data-gdlr-animation="fadeInLeft" data-gdlr-animation-duration="600ms" data-gdlr-animation-offset="0.8">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-image-item gdlr-core-item-pdb gdlr-core-left-align gdlr-core-item-pdlr">
                                                    <div
                                                        class="gdlr-core-image-item-wrap gdlr-core-media-image gdlr-core-image-item-style-round"
                                                        style="border-width: 0px; border-radius: 20px; -moz-border-radius: 20px; -webkit-border-radius: 20px;"
                                                    >
                                                        <img
                                                            src="uploads/apartment-2-number-img.jpg"
                                                            alt=""
                                                            width="550"
                                                            height="684"
                                                            title="apartment-2-number-img"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-30" id="gdlr-core-column-5">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 20px 0px 0px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" data-gdlr-animation="fadeInRight" data-gdlr-animation-duration="600ms" data-gdlr-animation-offset="0.8">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-left-align" style="padding-bottom: 25px;">
                                                    <div class="gdlr-core-text-box-item-content" style="text-transform: none; color: #0a0a0a;">
                                                        <p>
                                                            <span style="font-size: 55px; font-weight: 500; margin-right: 12px; letter-spacing: 4px;">5</span>
                                                            <span class="mmr30" style="font-size: 22px; font-weight: 400; margin-right: 80px; letter-spacing: 7px;">stars</span>
                                                            <span style="font-size: 55px; font-weight: 500; margin-right: 12px; letter-spacing: 4px;">07</span>
                                                            <span style="font-size: 22px; font-weight: 400; margin-right: 12px; letter-spacing: 7px;">apartments</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 35px;">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3
                                                            class="gdlr-core-title-item-title gdlr-core-skin-title class-test"
                                                            style="font-size: 42px; font-weight: 700; letter-spacing: 0px; line-height: 1.2; text-transform: none; color: #1c1c1c;"
                                                        >
                                                            Our apartments are located in the prime area of well-known cities<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-left-align" style="padding-bottom: 20px;">
                                                    <div class="gdlr-core-text-box-item-content" style="font-size: 19px; text-transform: none; color: #94959b;">
                                                        <p>
                                                            Search millions of apartments, houses, and private office suites<br />
                                                            for rent with our exclusive hotels &amp; apartments app.
                                                        </p>
                                                    </div>
                                                     <div class="tourmaster-room-search-size4 tourmaster-room-search-submit-wrap">
                                                            <input class="tourmaster-room-search-submit tourmaster-style-solid" type="submit" value="About Us" href="about.php"/>
                                                        </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-divider-item gdlr-core-divider-item-normal gdlr-core-item-pdlr gdlr-core-left-align">
                                                    <div class="gdlr-core-divider-container" style="max-width: 40px;">
                                                        <div class="gdlr-core-divider-line gdlr-core-skin-divider" style="border-color: #74c586; border-width: 3px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gdlr-core-pbf-wrapper gdlr-core-hide-in-mobile" style="margin: -200px 0px 0px 0px; padding: 0px 0px 30px 0px;">
                        <div class="gdlr-core-pbf-background-wrap"></div>
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
                                <div class="gdlr-core-pbf-element">
                                    <div class="gdlr-core-image-item gdlr-core-item-pdb gdlr-core-center-align gdlr-core-item-pdlr" style="padding-bottom: 0px;">
                                        <div class="gdlr-core-image-item-wrap gdlr-core-media-image gdlr-core-image-item-style-rectangle" style="border-width: 0px;">
                                            <img src="uploads/apartment-2-number-img-2.png" alt="" width="1122" height="395" title="apartment-2-number-img-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gdlr-core-pbf-wrapper" style="padding: 90px 0px 30px 0px;" id="gdlr-core-wrapper-2">
                        <div class="gdlr-core-pbf-background-wrap"></div>
                        <div class="gdlr-core-pbf-background-wrap" style="top: 50px;">
                            <div
                                class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js"
                                style="background-image: url(uploads/apartment2-blog-bg.png); background-repeat: no-repeat; background-position: top center;"
                                data-parallax-speed="0"
                            ></div>
                        </div>
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-6">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 0px 0px 30px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-center-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3 class="gdlr-core-title-item-title gdlr-core-skin-title class-test" style="font-size: 45px; font-weight: 700; letter-spacing: 0px; line-height: 1; text-transform: none;">
                                                            Our Rooms.<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-divider-item gdlr-core-divider-item-normal gdlr-core-item-pdlr gdlr-core-center-align">
                                                    <div class="gdlr-core-divider-container" style="max-width: 40px;">
                                                        <div class="gdlr-core-divider-line gdlr-core-skin-divider" style="border-color: #74c586; border-width: 3px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 20px;">
                                                    <div class="gdlr-core-text-box-item-content" style="font-size: 19px; text-transform: none; color: #94959b;">
                                                        <p>Choose from a wide range of exclusive rooms, hotels, and apartments.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="tourmaster-room-item clearfix tourmaster-room-item-style-grid" id="gdlr-core-room-1">
                                                    <div
                                                        class="gdlr-core-flexslider flexslider gdlr-core-js-2"
                                                        data-type="carousel"
                                                        data-column="3"
                                                        data-move="1"
                                                        data-nav="navigation-outer"
                                                        data-nav-parent="self"
                                                        data-vcenter-nav="1"
                                                        data-disable-autoslide="1"
                                                    >
                                                        <div class="gdlr-core-flexslider-custom-nav gdlr-core-style-navigation-outer gdlr-core-center-align" style="margin-top: -5px;">
                                                            <i class="icon-arrow-left flex-prev" style="color: #9e9e9e; font-size: 34px; left: -80px;"></i>
                                                            <i class="icon-arrow-right flex-next" style="color: #9e9e9e; font-size: 34px; right: -80px;"></i>
                                                        </div>
                                                        <ul class="slides">
                                                        <?php foreach ($rooms as $room): ?>
                                                            <li class="gdlr-core-item-mglr">
                                                                <div class="tourmaster-room-grid tourmaster-grid-frame">
                                                                    <div
                                                                        class="tourmaster-room-grid-inner"
                                                                        style="
                                                                            box-shadow: 0 20px 35px rgba(10, 10, 10, 0.09);
                                                                            -moz-box-shadow: 0 20px 35px rgba(10, 10, 10, 0.09);
                                                                            -webkit-box-shadow: 0 20px 35px rgba(10, 10, 10, 0.09);
                                                                            border-radius: 20px;
                                                                            -moz-border-radius: 20px;
                                                                            -webkit-border-radius: 20px;
                                                                        "
                                                                    >
                                                                        <div class="tourmaster-room-thumbnail tourmaster-media-image gdlr-core-outer-frame-element tourmaster-with-price<?php echo (!empty($room['discount_price']) && $room['discount_price'] < $room['price']) ? ' tourmaster-with-ribbon' : ''; ?>">
                                                                            <a href="single-room.html?room_id=<?php echo $room['room_id']; ?>">
                                                                                <img
                                                                                    src="<?php echo htmlspecialchars($room['room_image'] ?? 'uploads/default-room.jpg'); ?>"
                                                                                    alt="<?php echo htmlspecialchars($room['title']); ?>"
                                                                                    width="780"
                                                                                    height="595"
                                                                                />
                                                                            </a>
                                                                            <div
                                                                                class="tourmaster-price-wrap tourmaster-with-bg tourmaster-with-text-color"
                                                                                style="border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; background-color: #ffffff; color: #1e1e1e;"
                                                                            >
                                                                                <span class="tourmaster-head">From</span>
                                                                                <?php if (!empty($room['discount_price']) && $room['discount_price'] < $room['price']): ?>
                                                                                    <span class="tourmaster-price-discount">$<?php echo number_format($room['price'], 2); ?></span>
                                                                                    <span class="tourmaster-price">₹<?php echo number_format($room['discount_price'], 2); ?></span>
                                                                                <?php else: ?>
                                                                                    <span class="tourmaster-price">₹<?php echo number_format($room['price'], 2); ?></span>
                                                                                <?php endif; ?>
                                                                                <span class="tourmaster-tail"> / night</span>
                                                                            </div>
                                                                            <?php if (!empty($room['discount_price']) && $room['discount_price'] < $room['price']): ?>
                                                                                <div class="tourmaster-ribbon"><?php echo round((($room['price'] - $room['discount_price']) / $room['price']) * 100); ?>% Off</div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="tourmaster-room-content-wrap gdlr-core-skin-e-background gdlr-core-js" data-sync-height="room-item-90" style="padding-bottom: 30px;">
                                                                            <h3 class="tourmaster-room-title gdlr-core-skin-title" style="font-size: 21px; font-weight: 500; text-transform: none;">
                                                                                <a href="single-room.html?room_id=<?php echo $room['room_id']; ?>"><?php echo htmlspecialchars($room['title']); ?></a>
                                                                            </h3>
                                                                            <div class="tourmaster-info-wrap clearfix">
                                                                                <div class="tourmaster-info tourmaster-info-bed-type"><i class="gdlr-icon-double-bed2"></i><span class="tourmaster-tail"><?php echo htmlspecialchars($room['bed_size'] ?? '1 Bed'); ?></span></div>
                                                                                <div class="tourmaster-info tourmaster-info-guest-amount"><i class="gdlr-icon-group"></i><span class="tourmaster-tail"><?php echo htmlspecialchars($room['guest_capacity'] ?? 2); ?> Guests</span></div>
                                                                            </div>
                                                                            <div class="tourmaster-room-rating">
                                                                                <?php
                                                                                $rating = (float)$room['rating'];
                                                                                $fullStars = floor($rating);
                                                                                $halfStar = ($rating - $fullStars) >= 0.5;
                                                                                
                                                                                for ($i = 0; $i < $fullStars; $i++) echo '<i class="fa fa-star"></i>';
                                                                                if ($halfStar) echo '<i class="fa fa-star-half-o"></i>';
                                                                                for ($i = $fullStars + $halfStar; $i < 5; $i++) echo '<i class="fa fa-star-o"></i>';
                                                                                ?>
                                                                                <span class="tourmaster-room-rating-text"><?php echo number_format($rating, 1); ?> Stars</span>
                                                                            </div>
                                                                            <div class="tourmaster-location"><i class="icon-location-pin"></i><?php echo htmlspecialchars($room['location'] ?? 'Location N/A'); ?></div>
                                                                            <a
                                                                                class="tourmaster-read-more tourmaster-type-text"
                                                                                href="stay_detail.php?room_id=<?php echo $room['room_id']; ?>"
                                                                                style="color: #212121;"
                                                                            >
                                                                                Book Now<i class="icon-arrow-right"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-button-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align">
                                                    <a
                                                        class="gdlr-core-button gdlr-core-button-transparent gdlr-core-center-align gdlr-core-button-with-border"
                                                        href="stay.php"
                                                        style="
                                                            font-style: normal;
                                                            font-weight: 600;
                                                            color: #94959b;
                                                            padding: 14px 33px 14px 33px;
                                                            border-radius: 5px;
                                                            -moz-border-radius: 5px;
                                                            -webkit-border-radius: 5px;
                                                            border-color: #94959b;
                                                        "
                                                    >
                                                        <span class="gdlr-core-content">View All Stays</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gdlr-core-pbf-wrapper" style="padding: 90px 0px 30px 0px;" id="gdlr-core-wrapper-3">
                        <div class="gdlr-core-pbf-background-wrap"></div>
                        <div class="gdlr-core-pbf-background-wrap" style="top: 110px;">
                            <div
                                class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js"
                                style="background-image: url(uploads/apartment2-column-bg.png); background-repeat: no-repeat; background-position: top center;"
                                data-parallax-speed="0"
                            ></div>
                        </div>
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-7">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 0px 0px 90px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" style="max-width: 580px;">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-center-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3 class="gdlr-core-title-item-title gdlr-core-skin-title class-test" style="font-size: 45px; font-weight: 700; letter-spacing: 0px; line-height: 1; text-transform: none;">
                                                            Why Choose Us?<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-divider-item gdlr-core-divider-item-normal gdlr-core-item-pdlr gdlr-core-center-align">
                                                    <div class="gdlr-core-divider-container" style="max-width: 40px;">
                                                        <div class="gdlr-core-divider-line gdlr-core-skin-divider" style="border-color: #74c586; border-width: 3px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 20px;">
                                                    <div class="gdlr-core-text-box-item-content" style="font-size: 19px; text-transform: none; color: #94959b;">
                                                        <p>Search millions of apartments, houses, and private office suites for rent with our exclusive hotels &amp; apartments app.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-20 gdlr-core-column-first" id="gdlr-core-column-8">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 0px 0px 30px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-background-frame" style="margin: 0px 20px 0px 20px; border-width: 0px 0px 1px 0px; border-style: solid; border-color: #eaeaea;"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" data-gdlr-animation="fadeInUp" data-gdlr-animation-duration="600ms" data-gdlr-animation-offset="0.8">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-image-item gdlr-core-item-pdb gdlr-core-center-align gdlr-core-item-pdlr">
                                                    <div class="gdlr-core-image-item-wrap gdlr-core-media-image gdlr-core-image-item-style-rectangle" style="border-width: 0px;">
                                                        <img src="uploads/apartment2-col-1.png" alt="" width="60" height="60" title="apartment2-col-1" />
                                                    </div>
                                                </div>                  
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-center-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 25px;">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3 class="gdlr-core-title-item-title gdlr-core-skin-title class-test" style="font-size: 21px; font-weight: 400; letter-spacing: 0px; line-height: 1; text-transform: none;">
                                                            Middle of Downtown<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 20px;">
                                                    <div class="gdlr-core-text-box-item-content" style="font-size: 19px; text-transform: none; color: #94959b;">
                                                        <p>Choose from a wide range of exclusive rooms, hotels, and apartments.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-20" id="gdlr-core-column-9">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 0px 0px 30px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-background-frame" style="margin: 0px 20px 0px 20px; border-width: 0px 0px 1px 0px; border-style: solid; border-color: #eaeaea;"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" data-gdlr-animation="fadeInDown" data-gdlr-animation-duration="600ms" data-gdlr-animation-offset="0.8">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-image-item gdlr-core-item-pdb gdlr-core-center-align gdlr-core-item-pdlr">
                                                    <div class="gdlr-core-image-item-wrap gdlr-core-media-image gdlr-core-image-item-style-rectangle" style="border-width: 0px;">
                                                        <img src="uploads/apartment2-col-2.png" alt="" width="60" height="60" title="apartment2-col-2" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-center-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 25px;">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3 class="gdlr-core-title-item-title gdlr-core-skin-title class-test" style="font-size: 21px; font-weight: 400; letter-spacing: 0px; line-height: 1; text-transform: none;">
                                                            Clean Accommodation<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 20px;">
                                                    <div class="gdlr-core-text-box-item-content" style="font-size: 19px; text-transform: none; color: #94959b;">
                                                        <p>Choose from a wide range of exclusive rooms, hotels, and apartments.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-20" id="gdlr-core-column-10">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 0px 0px 30px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-background-frame" style="margin: 0px 20px 0px 20px; border-width: 0px 0px 1px 0px; border-style: solid; border-color: #eaeaea;"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" data-gdlr-animation="fadeInUp" data-gdlr-animation-duration="600ms" data-gdlr-animation-offset="0.8">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-image-item gdlr-core-item-pdb gdlr-core-center-align gdlr-core-item-pdlr">
                                                    <div class="gdlr-core-image-item-wrap gdlr-core-media-image gdlr-core-image-item-style-rectangle" style="border-width: 0px;">
                                                        <img src="uploads/apartment2-col-3.png" alt="" width="60" height="60" title="apartment2-col-3" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-center-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 25px;">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3 class="gdlr-core-title-item-title gdlr-core-skin-title class-test" style="font-size: 21px; font-weight: 400; letter-spacing: 0px; line-height: 1; text-transform: none;">
                                                            Airport Transfer<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 20px;">
                                                    <div class="gdlr-core-text-box-item-content" style="font-size: 19px; text-transform: none; color: #94959b;">
                                                        <p>Choose from a wide range of exclusive rooms, hotels, and apartments.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="gdlr-core-pbf-wrapper" style="padding: 80px 0px 60px 0px;" id="gdlr-core-wrapper-5">
                        <div class="gdlr-core-pbf-background-wrap"></div>
                        <div class="gdlr-core-pbf-background-wrap" style="top: 70px;">
                            <div
                                class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js"
                                style="background-image: url(uploads/bg-testimonail.jpg); background-repeat: no-repeat; background-position: top center;"
                                data-parallax-speed="0"
                            ></div>
                        </div>      
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-13">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 20px 0px 0px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap">
                                            <div
                                                class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js"
                                                style="
                                                    background-image: url(uploads/apartment2-testimonial-bg.png);
                                                    background-repeat: no-repeat;
                                                    background-position: top center;
                                                "
                                                data-parallax-speed="0"
                                            ></div>
                                        </div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" style="max-width: 660px;">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-center-align gdlr-core-title-item-caption-bottom gdlr-core-item-pdlr">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3 class="gdlr-core-title-item-title gdlr-core-skin-title class-test" style="font-size: 45px; font-weight: 700; letter-spacing: 0px; text-transform: none;">
                                                            Testimonial<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-divider-item gdlr-core-divider-item-normal gdlr-core-item-pdlr gdlr-core-center-align">
                                                    <div class="gdlr-core-divider-container" style="max-width: 40px;">
                                                        <div class="gdlr-core-divider-line gdlr-core-skin-divider" style="border-color: #74c586; border-width: 3px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 20px;">
                                                    <div class="gdlr-core-text-box-item-content" style="font-size: 19px; text-transform: none; color: #94959b;">
                                                        <p>All our hotels are fabulous, they are destinations unto themselves. We have crossed the globe to bring you only the best.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-14">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 70px 0px 0px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                            <div class="gdlr-core-pbf-element mb-5">
                                                <div class="gdlr-core-testimonial-item gdlr-core-item-pdb clearfix gdlr-core-testimonial-style-left-2 gdlr-core-item-pdlr">
                                                    <div
                                                        class="gdlr-core-flexslider flexslider gdlr-core-js-2 gdlr-core-bullet-style-round gdlr-core-color-bullet"
                                                        data-type="carousel"
                                                        data-column="2"
                                                        data-move="1"
                                                        data-nav="bullet"
                                                        data-controls-top-margin="70px"
                                                    >
                                                        <ul class="slides">
                                                            <?php 
                                                            // Include database connection and feedback fetch function
                                                            include 'config/db.php';
                                                            include 'includes/feedback_fetch.php';

                                                            // Fetch feedback from database
                                                            $feedbacks = fetchFeedback($conn, 5);

                                                            // Display feedback items
                                                            foreach ($feedbacks as $feedback): ?>
                                                                <li class="gdlr-core-item-mglr">
                                                                    <div class="gdlr-core-testimonial clearfix gdlr-core-with-frame">
                                                                        <div
                                                                            class="gdlr-core-testimonial-frame clearfix gdlr-core-skin-e-background gdlr-core-outer-frame-element"
                                                                            style="
                                                                                box-shadow: 0px 15px 35px rgba(0, 0, 0, 0.08);
                                                                                -moz-box-shadow: 0px 15px 35px rgba(0, 0, 0, 0.08);
                                                                                -webkit-box-shadow: 0px 15px 35px rgba(0, 0, 0, 0.08);
                                                                                border-radius: 20px 20px 20px 20px;
                                                                                -moz-border-radius: 20px 20px 20px 20px;
                                                                                -webkit-border-radius: 20px 20px 20px 20px;
                                                                            "
                                                                        >
                                                                            <div
                                                                                class="gdlr-core-testimonial-frame-border"
                                                                                style="border-radius: 20px 20px 20px 20px; -moz-border-radius: 20px 20px 20px 20px; -webkit-border-radius: 20px 20px 20px 20px;"
                                                                            ></div>
                                                                            <div class="gdlr-core-testimonial-author-image gdlr-core-media-image">
                                                                                <?php 
                                                                                $profile_pic = !empty($feedback['profile_pic']) && file_exists($feedback['profile_pic']) ? 
                                                                                    $feedback['profile_pic'] : 'uploads/customer1-150x150.jpg';
                                                                                $username = htmlspecialchars($feedback['username']);
                                                                                ?>
                                                                                <img
                                                                                    src="<?php echo $profile_pic; ?>"
                                                                                    alt="<?php echo $username; ?>"
                                                                                    width="150"
                                                                                    height="150"
                                                                                    title="<?php echo $username; ?>"
                                                                                />
                                                                                <div class="gdlr-core-testimonial-quote gdlr-core-quote-font gdlr-core-skin-icon">&#8220;</div>
                                                                            </div>
                                                                            <div class="gdlr-core-testimonial-content-wrap">
                                                                                <div class="gdlr-core-testimonial-content gdlr-core-info-font gdlr-core-skin-content" style="font-size: 18px; color: #656565; padding-bottom: 25px;">
                                                                                    <p>
                                                                                        <?php echo htmlspecialchars($feedback['feedback_message']); ?>
                                                                                    </p>
                                                                                </div>
                                                                                <div class="gdlr-core-testimonial-author-wrap clearfix">
                                                                                    <div class="gdlr-core-testimonial-author-content">
                                                                                        <div
                                                                                            class="gdlr-core-testimonial-title gdlr-core-title-font gdlr-core-skin-title"
                                                                                            style="color: #313131; font-size: 19px; font-weight: 600; font-style: normal; letter-spacing: 0px; text-transform: none;"
                                                                                        >
                                                                                            <?php echo htmlspecialchars($feedback['username']); ?>
                                                                                        </div>
                                                                                        <div
                                                                                            class="gdlr-core-testimonial-position gdlr-core-info-font gdlr-core-skin-caption"
                                                                                            style="color: #313131; font-size: 14px; font-style: normal; font-weight: 500;"
                                                                                        >
                                                                                            Customer
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <!-- button of give feedback -->
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <input class="tourmaster-room-search-submit tourmaster-style-solid" type="button" value="Give Feedback" data-bs-toggle="modal" data-bs-target="#feedbackModalLabel" style="margin-top: 20px; cursor: pointer;" />
                                                </div>
                                                
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gdlr-core-pbf-wrapper" style="padding: 140px 0px 110px 0px;" id="gdlr-core-wrapper-6">
                        <div class="gdlr-core-pbf-background-wrap" style="background-color: #0a0a0a;">
                            <div
                                class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js"
                                style="
                                    opacity: 0.7;
                                    background-image: url(uploads/trent-szmolnik-1271248-unsplash.jpg);
                                    background-size: cover;
                                    background-position: center;
                                "
                                data-parallax-speed="0"
                            ></div>
                        </div>      
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js" data-gdlr-animation="fadeIn" data-gdlr-animation-duration="600ms" data-gdlr-animation-offset="0.8">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-15">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" style="max-width: 690px;">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-center-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3 class="gdlr-core-title-item-title gdlr-core-skin-title class-test" style="font-size: 54px; font-weight: 400; letter-spacing: 0px; text-transform: none; color: #ffffff;">
                                                            Get Update For <span style="font-weight: 700;">Deals and Promotions</span><span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" data-skin="Newsletter Button" id="gdlr-core-column-16">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" style="max-width: 560px;">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-newsletter-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-style-curve">
                                                    <div class="newsletter newsletter-subscription">
                                                        <form class="gdlr-core-newsletter-form clearfix" method="post" action="#" onsubmit="return newsletter_check(this)">
                                                            <div class="gdlr-core-newsletter-email">
                                                                <input class="newsletter-email gdlr-core-skin-e-background gdlr-core-skin-e-content" placeholder="Your Email Address" type="email" name="ne" size="30" required />
                                                            </div>
                                                            <div class="gdlr-core-newsletter-submit"><input class="newsletter-submit" type="submit" value="Subscribe" /></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gdlr-core-pbf-wrapper" style="padding: 120px 0px 30px 0px;" id="gdlr-core-wrapper-7">
                        <div class="gdlr-core-pbf-background-wrap"></div>
                        <div class="gdlr-core-pbf-background-wrap" style="top: 120px;">
                            <div
                                class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js"
                                style="background-image: url(uploads/apartment2-blog-bg-1.png); background-repeat: no-repeat; background-position: top center;"
                                data-parallax-speed="0"
                            ></div>
                        </div>
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-17">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 0px 0px 25px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js" style="max-width: 700px;">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-center-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr">
                                                    <div class="gdlr-core-title-item-title-wrap">
                                                        <h3 class="gdlr-core-title-item-title gdlr-core-skin-title class-test" style="font-size: 45px; font-weight: 700; letter-spacing: 0px; line-height: 1; text-transform: none;">
                                                            News & Offers<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-divider-item gdlr-core-divider-item-normal gdlr-core-item-pdlr gdlr-core-center-align">
                                                    <div class="gdlr-core-divider-container" style="max-width: 40px;">
                                                        <div class="gdlr-core-divider-line gdlr-core-skin-divider" style="border-color: #74c586; border-width: 3px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 20px;">
                                                    <div class="gdlr-core-text-box-item-content" style="font-size: 19px; text-transform: none; color: #94959b;">
                                                        <p>All our hotels are fabulous, they are destinations unto themselves. We have crossed the globe to bring you only the best.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-blog-item gdlr-core-item-pdb clearfix gdlr-core-style-blog-image gdlr-core-item-pdlr" id="gdlr-core-blog-1">
                                                    <div class="gdlr-core-flexslider flexslider gdlr-core-js-2" data-type="carousel" data-column="3" data-move="1" data-nav="navigation-outer" data-nav-parent="self" data-vcenter-nav="1">
                                                        <div class="gdlr-core-flexslider-custom-nav gdlr-core-style-navigation-outer gdlr-core-center-align">
                                                            <i class="icon-arrow-left flex-prev" style="color: #cccccc; font-size: 34px; left: -70px;"></i>
                                                            <i class="icon-arrow-right flex-next" style="color: #cccccc; font-size: 34px; right: -70px;"></i>
                                                        </div>
                                                        <ul class="slides">
                                                            <?php
                                                            // Include database connection and news fetch function
                                                            include 'config/db.php';
                                                            include 'includes/news_fetch.php';
                                                            
                                                            // Fetch news from database
                                                            $news_items = fetchNews($conn, 10);
                                                            
                                                            // Display news items
                                                            foreach ($news_items as $news):
                                                            ?>
                                                            <li class="gdlr-core-item-mglr">
                                                                <div
                                                                    class="gdlr-core-blog-modern gdlr-core-with-image gdlr-core-hover-overlay-content gdlr-core-opacity-on-hover gdlr-core-zoom-on-hover gdlr-core-style-1 gdlr-core-outer-frame-element"
                                                                    style="border-width: 0px; border-radius: 20px; -moz-border-radius: 20px; -webkit-border-radius: 20px;"
                                                                >
                                                                    <div class="gdlr-core-blog-modern-inner">
                                                                        <div class="gdlr-core-blog-thumbnail gdlr-core-media-image">
                                                                            <img
                                                                                src="<?php echo htmlspecialchars($news['image_url']); ?>"
                                                                                alt="<?php echo htmlspecialchars($news['title']); ?>"
                                                                                title="<?php echo htmlspecialchars($news['title']); ?>"
                                                                                style="width: 100%; height: 250px; object-fit: cover; border-radius: 20px;"
                                                                            />
                                                                        </div>
                                                                        <div class="gdlr-core-blog-modern-content gdlr-core-center-align">
                                                                            <h3 class="gdlr-core-blog-title gdlr-core-skin-title text-truncate" style="font-size: 26px; font-style: normal; font-weight: 500; letter-spacing: 0px;" >
                                                                                <a href="news.php?news_id=<?php echo htmlspecialchars($news['news_id']); ?>"><?php echo htmlspecialchars($news['title']); ?></a>
                                                                            </h3>
                                                                            <div class="gdlr-core-blog-info-wrapper gdlr-core-skin-divider">
                                                                                <span class="gdlr-core-blog-info gdlr-core-blog-info-font gdlr-core-skin-caption gdlr-core-blog-info-date">
                                                                                    <span class="gdlr-core-blog-info-sep">•</span><span class="gdlr-core-head"><i class="gdlr-icon-clock"></i></span>
                                                                                    <a href="news.php"><?php echo formatDate($news['publish_date']); ?></a>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-18">
                                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding: 35px 0px 15px 0px;">
                                        <div class="gdlr-core-pbf-background-wrap"></div>
                                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                            <div class="gdlr-core-pbf-element">
                                                <div class="gdlr-core-button-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

            </div>
            <!-- Feedback Popup Modal -->
            <div class="modal fade" id="feedbackModalLabel" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-desktop-right">
                    <div class="modal-content" style="background-color: #ffffff; border: 1px solid #e0e0e0;">
                        <div class="modal-header" style="background-color: #0a0a0a; color: #ffffff; border-bottom: 1px solid #313131;">
                            <h5 class="modal-title text-white" id="feedbackModalLabel">Quick Feedback</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="background-color: #ffffff;">
                            <form id="feedbackForm">
                                <div class="mb-3">
                                    <label for="feedback_message" class="form-label" style="color: #313131;"><strong>Your Feedback *</strong></label>
                                    <textarea class="form-control" id="feedback_message" name="feedback_message" rows="4" placeholder="Share your feedback with us..." required style="background-color: #ffffff; border: 1px solid #94959b; color: #0a0a0a;"></textarea>
                                </div>
                            </form>
                            <div id="feedbackMessage" class="mt-3"></div>
                        </div>
                        <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e0e0e0;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #94959b; border: 1px solid #94959b; color: #ffffff;">Cancel</button>
                            <button type="button" class="btn btn-primary" id="submitFeedbackBtn" style="background-color: #0a0a0a; border: 1px solid #0a0a0a; color: #ffffff;">Submit Feedback</button>
                        </div>
                    </div>
                </div>
            </div>
            
<!-- Chatbot widget start -->
<div id="sb-chatbot" aria-live="polite">
  <button id="sb-chat-toggle" aria-label="Open student help chat" title="Chat with us">
    <!-- simple chat icon -->
    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path d="M4 18v-7a7 7 0 017-7h2a7 7 0 017 7v2a7 7 0 01-7 7H9l-5 3 0-5z" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </button>
  <div id="sb-chat-window" role="dialog" aria-modal="false" aria-label="Student help chat">
    <div id="sb-chat-header">
      <div>
        <strong>Student Help</strong>
        <div class="sb-sub">Ask about rooms, meals, bookings</div>
      </div>
      <button id="sb-chat-close" aria-label="Close chat" title="Close">×</button>
    </div>
    <div id="sb-chat-messages" class="sb-chat-messages"></div>
    <form id="sb-chat-form" autocomplete="off">
      <input id="sb-chat-input" type="text" inputmode="text" placeholder="Type your question..." aria-label="Message" />
      <button type="submit" id="sb-chat-send" aria-label="Send">Send</button>
    </form>
  </div>
</div>
            <!-- Custom CSS for modal positioning -->
            <style>
            /* Desktop: Position modal in bottom right */
            @media (min-width: 768px) {
                .modal-dialog-desktop-right {
                    position: fixed;
                    margin: auto;
                    width: 400px;
                    height: fit-content;
                    right: 20px;
                    bottom: 20px;
                    left: auto;
                    top: auto;
                    margin-top: 0;
                    margin-bottom: 0;
                }
                
                .modal-dialog-desktop-right .modal-content {
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                }
            }
            
            /* Mobile: Keep modal centered */
            @media (max-width: 767.98px) {
                .modal-dialog-desktop-right {
                    position: relative;
                    margin: 1.75rem auto;
                    width: auto;
                    max-width: 500px;
                }
            }
            </style>
             
             

             
<style>
  #sb-chatbot { position: fixed; right: 20px; bottom: 20px; z-index: 9999; font-family: inherit; }
  #sb-chat-toggle { width: 56px; height: 56px; border: none; border-radius: 50%; background: #2575fc; color: #fff; cursor: pointer; box-shadow: 0 8px 24px rgba(0,0,0,.18); display: flex; align-items: center; justify-content: center; }
  #sb-chat-toggle:focus { outline: 3px solid rgba(37,117,252,.35); }
  #sb-chat-window { display: none; width: 320px; max-height: 65vh; background: #fff; border: 1px solid #e8e8e8; border-radius: 14px; box-shadow: 0 20px 48px rgba(0,0,0,.22); overflow: hidden; }
  #sb-chat-header { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 14px; background: linear-gradient(135deg,#6a11cb,#2575fc); color: #fff; }
  #sb-chat-header .sb-sub { font-size: 12px; opacity: .85; }
  #sb-chat-close { border: none; background: transparent; color: #fff; font-size: 20px; line-height: 1; cursor: pointer; padding: 4px 6px; border-radius: 6px; }
  #sb-chat-close:focus { outline: 2px solid rgba(255,255,255,.6); }
  .sb-chat-messages { padding: 12px; overflow-y: auto; max-height: calc(65vh - 112px); background: #fafbfc; }
  .sb-msg { display: flex; margin-bottom: 10px; }
  .sb-msg.user { justify-content: flex-end; }
  .sb-bubble { max-width: 78%; padding: 8px 10px; border-radius: 12px; font-size: 14px; line-height: 1.35; }
  .sb-msg.user .sb-bubble { background: #2575fc; color: #fff; border-top-right-radius: 4px; }
  .sb-msg.bot .sb-bubble { background: #eef2f7; color: #23262a; border-top-left-radius: 4px; }
  #sb-chat-form { display: flex; gap: 8px; align-items: center; padding: 10px; background: #fff; border-top: 1px solid #eee; }
  #sb-chat-input { flex: 1; border: 1px solid #d0d5dd; border-radius: 10px; padding: 9px 10px; font-size: 14px; }
  #sb-chat-send { border: none; background: #2575fc; color: #fff; padding: 9px 12px; border-radius: 10px; cursor: pointer; }
  /* Responsive: stack above footer on small screens */
  @media (max-width: 480px) {
    #sb-chat-window { width: calc(100vw - 24px); right: 12px; }
    #sb-chatbot { right: 12px; bottom: 12px; }
  }
</style>

<script>
  (function() {
    var toggle = document.getElementById('sb-chat-toggle');
    var win = document.getElementById('sb-chat-window');
    var closeBtn = document.getElementById('sb-chat-close');
    var form = document.getElementById('sb-chat-form');
    var input = document.getElementById('sb-chat-input');
    var messages = document.getElementById('sb-chat-messages');

    function appendMessage(sender, html) {
      var row = document.createElement('div');
      row.className = 'sb-msg ' + sender;
      var bubble = document.createElement('div');
      bubble.className = 'sb-bubble';
      bubble.innerHTML = html;
      row.appendChild(bubble);
      messages.appendChild(row);
      messages.scrollTop = messages.scrollHeight;
    }

    function greet() {
      appendMessage('bot', 'Hi! I\'m the Stay assistant. Ask me about rooms, meals, bookings, check‑in/out, or how to login/register.');
    }

    async function fetchAnswer(text){
      try {
        const res = await fetch('chatbot_api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ q: text })
        });
        if (!res.ok) throw new Error('Network error');
        const data = await res.json();
        if (data && data.answer) return data.answer;
      } catch (e) { /* fall back */ }
      return fallbackAnswer(text);
    }

    function fallbackAnswer(userText) {
      var t = (userText || '').toLowerCase();
      function link(href, text){ return '<a href="' + href + '">' + text + '</a>'; }
      if (/(room|rooms)/.test(t) && /(price|cost|rate|charge)/.test(t)) {
        return 'Rooms start from budget‑friendly rates. Browse the Rooms section and click “Check Details”.';
      }
      if (/(room|rooms)/.test(t) && /(available|availability|vacancy|free)/.test(t)) {
        return 'Availability changes daily. Open a room to see the latest details.';
      }
      if (/(book|booking|reserve|reservation)/.test(t)) {
        return 'Open a room and proceed to booking. For help, use ' + link('contact.php','Contact');
      }
      if (/(meal|food|menu|breakfast|lunch|dinner|veg|non[- ]?veg)/.test(t)) {
        return 'We provide Veg and Non‑Veg meal options. See the Meals section for today’s offerings.';
      }
      if (/(check[- ]?in|check[- ]?out)/.test(t)) {
        return 'Check‑in from 2:00 PM, check‑out until 11:00 AM.';
      }
      if (/(cancel|refund)/.test(t)) {
        return 'Cancellation/refunds depend on the booking. Please contact us via ' + link('contact.php','Contact');
      }
      if (/(login|sign in)/.test(t)) {
        return 'Login here: ' + link('login.php','login.php');
      }
      if (/(register|sign up|create account)/.test(t)) {
        return 'Register here: ' + link('register.php','register.php');
      }
      if (/(contact|support|help)/.test(t)) {
        return 'Reach us via ' + link('contact.php','contact.php') + '. We\'re happy to help!';
      }
      return 'I can help with rooms, meals, bookings, check‑in/out, login/registration, and contact. Try asking: “What are room prices?”';
    }

    function openChat() {
      if (win.style.display !== 'block') {
        win.style.display = 'block';
        if (!messages.childElementCount) { greet(); }
        input.focus();
      }
    }
    function closeChat() { win.style.display = 'none'; }

    toggle.addEventListener('click', function(){
      var open = win.style.display === 'block';
      if (open) { closeChat(); } else { openChat(); }
    });
    closeBtn.addEventListener('click', closeChat);
    toggle.addEventListener('keydown', function(e){ if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle.click(); }});

    form.addEventListener('submit', async function(e){
      e.preventDefault();
      var text = (input.value || '').trim();
      if (!text) return;
      appendMessage('user', text.replace(/</g,'&lt;'));
      input.value = '';
      appendMessage('bot', 'Typing…');
      const last = messages.lastElementChild; // loading bubble
      const ans = await fetchAnswer(text);
      if (last && last.classList.contains('sb-msg') && last.querySelector('.sb-bubble') && last.querySelector('.sb-bubble').innerText === 'Typing…') {
        last.remove();
      }
      appendMessage('bot', ans);
    });
  })();
</script>
<!-- Chatbot widget end -->

<?php
include 'includes/footer.php';
?>
