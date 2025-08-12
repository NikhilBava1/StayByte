<?php
session_start();
if(isset($_SESSION['user_id'])&&isset($_SESSION['role_id'])){
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['role_id'];
}
?>
<!DOCTYPE html>
<html lang="en-US" class="no-js">
<!-- Mirrored from max-themes.net/demos/hotale/hotale/apartment2/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 09 Jul 2025 06:54:25 GMT -->
<head>
<script>

</script>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>StayByte &#8211; Hospital Management System</title>
    


    <link rel="stylesheet"  href="js/plugins/goodlayers-core/plugins/fontawesome/font-awesome.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="js/plugins/goodlayers-core/plugins/fa5/fa5.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="js/plugins/goodlayers-core/plugins/elegant/elegant-font.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="js/plugins/goodlayers-core/plugins/ionicons/ionicons.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="js/plugins/goodlayers-core/plugins/simpleline/simpleline.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="js/plugins/goodlayers-core/plugins/gdlr-custom-icon/gdlr-custom-icon.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="js/plugins/goodlayers-core/plugins/style.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="js/plugins/goodlayers-core/include/css/page-builder.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="js/plugins/tourmaster/tourmaster.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="css/tourmaster-global-style-custom.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="js/plugins/tourmaster/room/tourmaster-room.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="css/tourmaster-room-style-customa5b5.css?1653843108&amp;ver=6.0.1" type="text/css" media="all" />
    <link rel="stylesheet"  href="css/style-core.css" type="text/css" media="all" />
    <link rel="stylesheet"  href="css/hotale-style-customafa1.css?1653801118&amp;ver=6.0.1" type="text/css" media="all" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"  href="css/student_dash.css" type="text/css" media="all" />


    <link rel="stylesheet"  href="css/style-core.css" type="text/css" media="all" />
    <link
        rel="stylesheet"
        id="gdlr-core-google-font-css"
        href="https://fonts.googleapis.com/css?family=Jost%3A100%2C200%2C300%2Cregular%2C500%2C600%2C700%2C800%2C900%2C100italic%2C200italic%2C300italic%2Citalic%2C500italic%2C600italic%2C700italic%2C800italic%2C900italic%7CAmiri%3Aregular%2Citalic%2C700%2C700italic%7CAllison%3Aregular&amp;subset=cyrillic%2Clatin%2Clatin-ext%2Carabic%2Cvietnamese&amp;ver=6.0.1"
        type="text/css"
        media="all"
    />

    <style>
/* Card hover animation for room cards */
.tourmaster-room-grid4 {
    transition: transform 0.25s cubic-bezier(0.4,0,0.2,1), box-shadow 0.25s cubic-bezier(0.4,0,0.2,1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.tourmaster-room-grid4:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 8px 24px rgba(0,0,0,0.13), 0 1.5px 6px rgba(0,0,0,0.08);
    z-index: 2;
}
</style>


</head>



<body
    class="home page-template-default page page-id-15360 theme-hotale gdlr-core-body tourmaster-body woocommerce-no-js hotale-body hotale-body-front hotale-full hotale-with-sticky-navigation hotale-blockquote-style-3 gdlr-core-link-to-lightbox"
    data-home-url="index.php" >

    <div class="hotale-mobile-header-wrap">
        <div class="hotale-mobile-header hotale-header-background hotale-style-slide hotale-sticky-mobile-navigation" id="hotale-mobile-header">
            <div class="hotale-mobile-header-container hotale-container clearfix">
                <div class="hotale-logo hotale-item-pdlr">
                    <div class="hotale-logo-inner">
                        <a class="hotale-fixed-nav-logo" href="index.php">
                            <img
                                src="upload/apartment2-logox2.png"
                                alt=""
                                width="147"
                                height="40"
                                title="apartment2-logox1"
                            />
                        </a>
                        <a class="hotale-orig-logo" href="index.php">
                            <img src="upload/apartment2-logox2.png" alt="" width="294" height="80" title="apartment2-logox2" />
                        </a>
                    </div>
                </div>
                <div class="hotale-mobile-menu-right">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="tourmaster-user-top-bar tourmaster-logged-in tourmaster-style-2">
                            <a href="student_dashboard.php" class="menu-item hotale-normal-menu"><i class="icon_house_alt"></i><span class="tourmaster-text">Dashboard</span></a>
                        </div>
                    <?php else: ?>
                        <div
                            class="tourmaster-user-top-bar tourmaster-guest tourmaster-style-2"
                            data-redirect="index.php"
                            data-ajax-url="#"
                        >
                            <span class="tourmaster-user-top-bar-login" data-tmlb="login"><i class="icon_lock_alt"></i><span class="tourmaster-text">Login</span></span>
                            <div class="tourmaster-lightbox-content-wrap" data-tmlb-id="login">
                                <div class="tourmaster-lightbox-head">
                                    <h3 class="tourmaster-lightbox-title">Login</h3>
                                    <i class="tourmaster-lightbox-close icon_close"></i>
                                </div>
                                <?php
                                    include 'mobile_login.php';
                                ?>  
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- This must be outside the PHP if/else! -->
                    <div class="hotale-mobile-menu">
                        <a class="hotale-mm-menu-button hotale-mobile-menu-button hotale-mobile-button-hamburger" href="#hotale-mobile-menu"><span></span></a>
                        <div class="hotale-mm-menu-wrap hotale-navigation-font" id="hotale-mobile-menu" data-slide="right">
                            <ul id="menu-main-navigation" class="m-menu">
                                <li class="menu-item menu-item-home current-menu-item  menu-item-has-children" >
                                    <a href="index.php" aria-current="page">Home</a>
                                    
                                </li>
                               
                                <li class="menu-item menu-item-has-children">
                                    <a  href="about-us-3.php">About Us</a>
                                </li>
                                <li class="menu-item menu-item-has-children">
                                    <a  href="stay.php">Stay</a>
                                </li>
                                <li class="menu-item"><a href="meal.php">Meal</a></li>
                                <li class="menu-item"><a href="our-team.php">Our Team</a></li>
                                <li class="menu-item"><a href="contact.php">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hotale-body-outer-wrapper">
        <div class="hotale-body-wrapper clearfix hotale-with-frame">
            <header class="hotale-header-wrap hotale-header-style-plain hotale-style-center-menu hotale-sticky-navigation hotale-style-fixed" data-navigation-offset="75">
                <div class="hotale-header-background"></div>
                <div class="hotale-header-container hotale-header-full">
                    <div class="hotale-header-container-inner clearfix">
                        <div class="hotale-logo hotale-item-pdlr">
                            <div class="hotale-logo-inner">
                                <a class="hotale-fixed-nav-logo" href="index.php">
                                    <img
                                        src="upload/apartment2-logox2.png"
                                        alt=""
                                        width="147"
                                        height="40"
                                        title="apartment2-logox1"
                                    />
                                </a>
                                <a class="hotale-orig-logo" href="index.php">
                                    <img
                                        src="upload/apartment2-logox2.png"
                                        alt=""
                                        width="147"
                                        height="40"
                                        title="apartment2-logox1"
                                    />
                                </a>
                            </div>
                        </div>
                        <div class="hotale-navigation hotale-item-pdlr clearfix">
                            <div class="hotale-main-menu" id="hotale-main-menu">
                                <ul id="menu-main-navigation-1" class="sf-menu">
                                    <li
                                        class="menu-item menu-item-home current-menu-item  menu-item-has-children hotale-normal-menu"
                                    >
                                        <a href="index.php" class="sf-with-ul-pre">Home</a>
                                       
                                        
                                    </li>
                                    <li class="menu-item menu-item-has-children hotale-normal-menu">
                                        <a href="about-us-3.php" class="sf-with-ul-pre">About Us</a>
                                      
                                    </li>
                                    <li class="menu-item menu-item-has-children hotale-normal-menu">
                                        <a href="stay.php" class="sf-with-ul-pre">Stay</a>
                                        
                                    </li>
                                    <li class="menu-item hotale-normal-menu">
                                        <a href="meals.php">meal</a>    
                                    </li>
                                    <li class="menu-item hotale-normal-menu">
                                            <a href="our-team.php">Our Team</a>
                                    </li>
                                    <li class="menu-ite hotale-normal-menu"><a href="contact.php">Contact</a></li>
                                </ul>
                                <div class="hotale-navigation-slide-bar hotale-navigation-slide-bar-style-2 hotale-left" data-size-offset="0" data-width="19px" id="hotale-navigation-slide-bar"></div>
                            </div>
                            <div class="hotale-main-menu-right-wrap clearfix hotale-item-mglr hotale-navigation-top">
                               
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <div class="tourmaster-user-top-bar tourmaster-logged-in tourmaster-style-2">
                                        <a href="student_dashboard.php" class="menu-item hotale-normal-menu"><span class="tourmaster-text">Dashboard</span></a>
                                    </div>
                                <?php else: ?>
                                    <div
                                        class="tourmaster-user-top-bar tourmaster-guest tourmaster-style-2"
                                        data-redirect="index.php"
                                        data-ajax-url="#"
                                    >
                                        <span class="tourmaster-user-top-bar-login" data-tmlb="login"><i class="icon_lock_alt"></i><span class="tourmaster-text">Login</span></span>
                                        <div class="tourmaster-lightbox-content-wrap" data-tmlb-id="login">
                                            <div class="tourmaster-lightbox-head">
                                                
                                                <h3 class="tourmaster-lightbox-title">Login</h3>
                                                <i class="tourmaster-lightbox-close icon_close"></i>
                                            </div>
                                            <?php
                                                include 'desk_login.php';
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- hotale-navigation -->
                    </div>
                    <!-- hotale-header-inner -->
                </div>
                <!-- hotale-header-container -->
            </header>
            <!-- header -->