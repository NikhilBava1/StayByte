<?php
include 'includes/header.php';
include 'config/db.php';


$perPage = 9;
$sql = "SELECT meal_id, meal_title, description, price, rating, meal_type, items_included, meal_address, image_url, status, created_at FROM meals LIMIT 0, $perPage";
$result = mysqli_query($conn, $sql);
$meals = mysqli_fetch_all($result, MYSQLI_ASSOC);

$totalRooms = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM meals"));
$totalPages = ceil($totalRooms / $perPage);
mysqli_close($conn);
?>          

            <div class="hotale-page-title-wrap hotale-style-custom hotale-center-align">
                <div class="hotale-header-transparent-substitute"></div>
                <div class="hotale-page-title-overlay"></div>
                <div class="hotale-page-title-container hotale-container">
                    <div class="hotale-page-title-content hotale-item-pdlr"><h1 class="hotale-page-title">Meals</h1></div>
                </div>
            </div>
            <div class="hotale-page-wrapper" id="hotale-page-wrapper">
                <div class="gdlr-core-page-builder-body">
                    <div class="gdlr-core-pbf-wrapper" style="padding: 70px 0px 20px 0px;">
                        <div class="gdlr-core-pbf-background-wrap"></div>
                        <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                            <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container-custom" style="max-width: 980px;">
                                <div class="gdlr-core-pbf-element">
                                    <div class="tourmaster-room-item clearfix tourmaster-room-item-style-side-thumbnail">
                                        <div class="tourmaster-room-item-holder gdlr-core-js-2 clearfix" data-layout="fitrows">
                                            <div class="gdlr-core-item-list tourmaster-item-pdlr">
                                                <div class="tourmaster-room-side-thumbnail" style="margin-bottom: 35px;">
                                                    <?php foreach ($meals as $meal): ?>
                                                        <div class="tourmaster-room-side-thumbnail-inner" style="border:1px solid #e5e5e5; border-radius:12px; margin-bottom:30px; overflow:hidden; display:flex; background:#fff;">
                                                            <!-- Image Section -->
                                                            <div class="tourmaster-room-thumbnail tourmaster-media-image" style="flex:0 0 260px; max-width:260px; overflow:hidden;">
                                                                <a href="meal_details.php?meal_id=<?php echo $meal['meal_id']; ?>">
                                                                    <img src="<?php echo htmlspecialchars($meal['image_url']); ?>" alt="" style="width:100%; height:180px; object-fit:cover;" />
                                                                </a>
                                                            </div>
                                                            <!-- Content Section -->
                                                            <div class="tourmaster-room-content-wrap gdlr-core-skin-e-background" style="flex:1; padding:24px 32px;">
                                                                <h3 class="tourmaster-room-title gdlr-core-skin-title" style="font-size: 26px; font-weight: 600; margin-bottom:10px;">
                                                                    <a href="meal_details.php?meal_id=<?php echo $meal['meal_id']; ?>"><?php echo htmlspecialchars($meal['meal_title']); ?></a>
                                                                </h3>
                                                                <div style="display:flex; flex-wrap:wrap; gap:18px 32px; margin-bottom:10px;">
                                                                    <div><strong>Type:</strong> <?php echo htmlspecialchars($meal['meal_type']); ?></div>
                                                                    <div><strong>Rating:</strong> <?php echo htmlspecialchars($meal['rating']); ?> ⭐</div>
                                                                    <div><strong>Price:</strong> ₹<?php echo htmlspecialchars($meal['price']); ?></div>
                                                                </div>
                                                                <div style="margin-bottom:10px;">
                                                                    <strong>Included:</strong> <?php echo htmlspecialchars($meal['items_included']); ?>
                                                                </div>
                                                                <div style="margin-bottom:10px;">
                                                                    <strong>Address:</strong> <?php echo htmlspecialchars($meal['meal_address']); ?>
                                                                </div>
                                                                <div style="margin-bottom:10px;" class="text-truncate">
                                                                    <strong>Description:</strong> <?php echo htmlspecialchars($meal['description']); ?>
                                                                </div>

                                                                <div class="tourmaster-bottom" style="display:flex; align-items:center; justify-content:space-between; margin-top:18px;">
                                                                    <a class="tourmaster-read-more tourmaster-type-text" href="meal_details.php?meal_id=<?php echo $meal['meal_id']; ?>" style="font-weight:500; color:#13c5dd;">Enquiry <i class="icon-arrow-right"></i></a>
                                                                    <div class="tourmaster-price-wrap tourmaster-no-bg" style="font-size:18px;">
                                                                        <span class="tourmaster-head">From</span>
                                                                        <span class="tourmaster-price" style="font-weight:600;">₹<?php echo htmlspecialchars($meal['price']); ?></span>
                                                                        <span class="tourmaster-tail"> / night</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
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
<?php
include 'includes/footer.php';
?>  