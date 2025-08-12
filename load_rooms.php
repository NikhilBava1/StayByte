<?php
include 'config/db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
$start = ($page - 1) * $perPage;

$sql = "SELECT * FROM rooms LIMIT $start, $perPage";
$result = mysqli_query($conn, $sql);
$rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($conn);

foreach ($rooms as $idx => $room): ?>
    <div class="gdlr-core-item-list tourmaster-item-pdlr tourmaster-column-20<?php echo ($idx % 3 === 0) ? ' tourmaster-column-first' : ''; ?>">
        <div class="tourmaster-room-grid4 tourmaster-grid-frame" style="margin-bottom: 40px;">
            <div class="tourmaster-room-grid-inner" style="border-width: 1px 1px 1px 1px; border-color: #e5e5e5; border-radius: 20px;">
                <div class="tourmaster-room-thumbnail tourmaster-media-image tourmaster-with-price">
                    <a href="single-room.html?room_id=<?php echo $room['room_id']; ?>">
                        <img src="<?php echo htmlspecialchars($room['room_image']); ?>" alt="<?php echo htmlspecialchars($room['title']); ?>" width="780" height="595" />
                    </a>
                    <div class="tourmaster-price-wrap tourmaster-with-bg"><span class="tourmaster-head">From</span><span class="tourmaster-price">$<?php echo htmlspecialchars($room['price']); ?></span></div>
                </div>
                <div class="tourmaster-room-content-wrap gdlr-core-skin-e-background gdlr-core-js" style="padding-top: 50px; padding-bottom: 45px;">
                    <h3 class="tourmaster-room-title gdlr-core-skin-title" style="text-transform: none;">
                        <a href="single-room.html?room_id=<?php echo $room['room_id']; ?>"><?php echo htmlspecialchars($room['title']); ?></a>
                    </h3>
                    <div class="tourmaster-room-rating">
                        <?php
                        $rating = (float)$room['rating'];
                        $fullStars = floor($rating);
                        $halfStar = ($rating - $fullStars) >= 0.5;
                        for ($i = 0; $i < $fullStars; $i++) echo '<i class="fa fa-star"></i>';
                        if ($halfStar) echo '<i class="fa fa-star-half-o"></i>';
                        for ($i = $fullStars + $halfStar; $i < 5; $i++) echo '<i class="fa fa-star-o"></i>';
                        ?>
                        <span class="tourmaster-room-rating-text"><?php echo htmlspecialchars($room['rating']); ?> Stars</span>
                    </div>
                    <div class="tourmaster-info-wrap clearfix">
                        <div class="tourmaster-info tourmaster-info-bed-type"><i class="gdlr-icon-double-bed2"></i><span class="tourmaster-tail"><?php echo htmlspecialchars($room['bed_size']); ?></span></div>
                        <div class="tourmaster-info tourmaster-info-guest-amount"><i class="gdlr-icon-group"></i><span class="tourmaster-tail"><?php echo htmlspecialchars($room['guest_capacity']); ?> Guests</span></div>
                    </div>
                    <a class="tourmaster-read-more tourmaster-type-text" href="single-room.html?room_id=<?php echo $room['room_id']; ?>">Check Details<i class="icon-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>