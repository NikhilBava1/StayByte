<?php
// stay_details.php
// FULL updated file: stay details view + AJAX endpoints for enquiry & rating
// NOTE: Adjust include paths or table/column names as needed to match your environment.

// Database connection (must set $conn as mysqli instance)
include 'config/db.php';

// -----------------------------
// Helper functions
// -----------------------------
function json_response($arr) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arr);
    exit;
}

function safe_int($v) {
    return (int)$v;
}

function safe_str($v) {
    return trim((string)$v);
}

function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// -----------------------------
// AJAX endpoint handling (POST)
// Important: handle POST before any HTML output to avoid mixing HTML + JSON
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Ensure DB present
    if (!isset($conn) || !($conn instanceof mysqli)) {
        json_response(['success' => false, 'message' => 'Database connection not available']);
    }

    $action = $_POST['action'];

    // --------- Submit enquiry ----------
    if ($action === 'submit_enquiry') {
        // Require login
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'Please login first.']);
        }

        $user_id = safe_int($_SESSION['user_id']);
        $room_id = safe_int($_POST['room_id'] ?? 0);
        $message = safe_str($_POST['message'] ?? '');

        if ($room_id <= 0 || $message === '') {
            json_response(['success' => false, 'message' => 'Invalid input. Provide a message and valid room id.']);
        }

        // Validate room exists
        $chk = $conn->prepare("SELECT room_id, title FROM rooms WHERE room_id = ? LIMIT 1");
        if (!$chk) json_response(['success' => false, 'message' => 'Database error (prepare).']);
        $chk->bind_param("i", $room_id);
        $chk->execute();
        $resChk = $chk->get_result();
        if (!$resChk || $resChk->num_rows === 0) {
            $chk->close();
            json_response(['success' => false, 'message' => 'Room not found.']);
        }
        $room_row = $resChk->fetch_assoc();
        $chk->close();

        // Insert enquiry (assumes enquiries table exists with appropriate columns)
        $ins = $conn->prepare("INSERT INTO enquiries (room_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())");
        if (!$ins) json_response(['success' => false, 'message' => 'Database error (prepare insert).']);
        $ins->bind_param("iis", $room_id, $user_id, $message);
        if ($ins->execute()) {
            $ins->close();
            json_response(['success' => true, 'message' => 'Enquiry sent successfully.']);
        } else {
            $err = $ins->error;
            $ins->close();
            json_response(['success' => false, 'message' => 'Database error: ' . $err]);
        }
    }

    // --------- Submit rating ----------
    if ($action === 'submit_rating') {
        // Require login
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'Please login first.']);
        }

        $user_id = safe_int($_SESSION['user_id']);
        $room_id = safe_int($_POST['room_id'] ?? 0);
        $rating = safe_int($_POST['rating'] ?? 0);

        if ($room_id <= 0 || $rating < 1 || $rating > 5) {
            json_response(['success' => false, 'message' => 'Invalid input. Rating must be between 1 and 5.']);
        }

        // Ensure room exists
        $chk = $conn->prepare("SELECT room_id FROM rooms WHERE room_id = ? LIMIT 1");
        if (!$chk) json_response(['success' => false, 'message' => 'Database error (prepare).']);
        $chk->bind_param("i", $room_id);
        $chk->execute();
        $resChk = $chk->get_result();
        if (!$resChk || $resChk->num_rows === 0) {
            $chk->close();
            json_response(['success' => false, 'message' => 'Room not found.']);
        }
        $chk->close();

        // Check if user already rated this stay
        $sel = $conn->prepare("SELECT id FROM rating WHERE stay_id = ? AND user_id = ? LIMIT 1");
        if (!$sel) json_response(['success' => false, 'message' => 'Database error (prepare select).']);
        $sel->bind_param("ii", $room_id, $user_id);
        $sel->execute();
        $sel->store_result();

        if ($sel->num_rows > 0) {
            // Update existing rating
            $sel->close();
            $upd = $conn->prepare("UPDATE rating SET rating = ?, created_at = NOW() WHERE stay_id = ? AND user_id = ?");
            if (!$upd) json_response(['success' => false, 'message' => 'Database error (prepare update).']);
            $upd->bind_param("iii", $rating, $room_id, $user_id);
            if (!$upd->execute()) {
                $err = $upd->error;
                $upd->close();
                json_response(['success' => false, 'message' => 'Database error updating rating: ' . $err]);
            }
            $upd->close();
        } else {
            // Insert new rating
            $sel->close();
            $ins = $conn->prepare("INSERT INTO rating (stay_id, user_id, rating, created_at) VALUES (?, ?, ?, NOW())");
            if (!$ins) json_response(['success' => false, 'message' => 'Database error (prepare insert rating).']);
            $ins->bind_param("iii", $room_id, $user_id, $rating);
            if (!$ins->execute()) {
                $err = $ins->error;
                $ins->close();
                json_response(['success' => false, 'message' => 'Database error inserting rating: ' . $err]);
            }
            $ins->close();
        }

        // Recalculate average and count
        $avgq = $conn->prepare("SELECT COUNT(*) AS cnt, AVG(rating) AS avg_rating FROM rating WHERE stay_id = ?");
        if (!$avgq) json_response(['success' => false, 'message' => 'Database error (prepare avg).']);
        $avgq->bind_param("i", $room_id);
        $avgq->execute();
        $avgRes = $avgq->get_result();
        $avg = 0;
        $cnt = 0;
        if ($avgRes && $avgRow = $avgRes->fetch_assoc()) {
            $cnt = (int)$avgRow['cnt'];
            $avg = $avgRow['avg_rating'] !== null ? round(floatval($avgRow['avg_rating']), 2) : 0;
        }
        $avgq->close();

        json_response(['success' => true, 'message' => 'Rating saved', 'avg' => $avg, 'count' => $cnt]);
    }

    // Unknown action
    json_response(['success' => false, 'message' => 'Unknown action.']);
}

// -----------------------------
// GET request: Render page
// -----------------------------

include 'includes/header.php'; // include header here (after POST handling)

// Get room id from GET (safe cast)
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
if ($room_id <= 0) {
    echo "<div class='container'><p>Invalid room id.</p></div>";
    include 'includes/footer.php';
    exit;
}

// Fetch logged-in user (if any)
$user = null;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $uStmt = $conn->prepare("SELECT user_id, username, email FROM users WHERE user_id = ? LIMIT 1");
    if ($uStmt) {
        $uStmt->bind_param("i", $uid);
        $uStmt->execute();
        $uRes = $uStmt->get_result();
        if ($uRes && $uRes->num_rows > 0) {
            $user = $uRes->fetch_assoc();
        }
        $uStmt->close();
    }
}

// Fetch room details from rooms table
$rStmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ? LIMIT 1");
if (!$rStmt) {
    echo "<div class='container'><p>Database error (rooms prepare failed).</p></div>";
    include 'includes/footer.php';
    exit;
}
$rStmt->bind_param("i", $room_id);
$rStmt->execute();
$rRes = $rStmt->get_result();
if (!$rRes || $rRes->num_rows === 0) {
    $rStmt->close();
    echo "<div class='container'><p>Room not found.</p></div>";
    include 'includes/footer.php';
    exit;
}
$room = $rRes->fetch_assoc();
$rStmt->close();

// Fetch room amenities
$room_amenities = [];
$aStmt = $conn->prepare("SELECT facility_name FROM room_amenities WHERE room_id = ?");
if ($aStmt) {
    $aStmt->bind_param("i", $room_id);
    $aStmt->execute();
    $aRes = $aStmt->get_result();
    if ($aRes) {
        while ($row = $aRes->fetch_assoc()) {
            $room_amenities[] = $row;
        }
    }
    $aStmt->close();
}

// Fetch room images ordered by image_id (your schema uses image_id)
$room_images = [];
$iStmt = $conn->prepare("SELECT image_id, image_url FROM rooms_images WHERE room_id = ? ORDER BY image_id ASC");
if ($iStmt) {
    $iStmt->bind_param("i", $room_id);
    $iStmt->execute();
    $iRes = $iStmt->get_result();
    if ($iRes) {
        while ($row = $iRes->fetch_assoc()) {
            $room_images[] = $row;
        }
    }
    $iStmt->close();
}

// Fetch average rating and total ratings for this stay (rating table uses stay_id)
$avgRating = 0;
$totalRatings = 0;
$avgStmt = $conn->prepare("SELECT COUNT(*) AS cnt, AVG(rating) AS avg_rating FROM rating WHERE stay_id = ?");
if ($avgStmt) {
    $avgStmt->bind_param("i", $room_id);
    $avgStmt->execute();
    $avgRes = $avgStmt->get_result();
    if ($avgRes && $avgRow = $avgRes->fetch_assoc()) {
        $totalRatings = (int)$avgRow['cnt'];
        $avgRating = $avgRow['avg_rating'] !== null ? round(floatval($avgRow['avg_rating']), 2) : 0;
    }
    $avgStmt->close();
}

// Fetch user's rating if logged in
$userRating = null;
if ($user) {
    $urStmt = $conn->prepare("SELECT rating FROM rating WHERE stay_id = ? AND user_id = ? LIMIT 1");
    if ($urStmt) {
        $urStmt->bind_param("ii", $room_id, $user['user_id']);
        $urStmt->execute();
        $urRes = $urStmt->get_result();
        if ($urRes && $urRes->num_rows > 0) {
            $userRating = (int)$urRes->fetch_assoc()['rating'];
        }
        $urStmt->close();
    }
}

// Close DB connection at the end (we'll close later)

// -------------- Begin HTML output --------------
?>
<!-- Page wrapper -->
<div class="hotale-page-wrapper" id="hotale-page-wrapper">
    <div class="tourmaster-room-single-header-title-wrap" style="border-radius:20px;">
        <div class="tourmaster-room-single-header-background-overlay"></div>
        <div class="tourmaster-container">
            <h1 class="tourmaster-item-pdlr"><?php echo esc($room['title'] ?? $room['room_name'] ?? 'Room'); ?></h1>
        </div>
    </div>

    <div class="gdlr-core-page-builder-body">
        <div class="gdlr-core-pbf-sidebar-wrapper gdlr-core-sticky-sidebar gdlr-core-js" id="gdlr-core-sidebar-wrapper-1">
            <div class="gdlr-core-pbf-sidebar-container gdlr-core-line-height-0 clearfix gdlr-core-js gdlr-core-container">
                <div class="gdlr-core-pbf-sidebar-content gdlr-core-column-45 gdlr-core-pbf-sidebar-padding gdlr-core-line-height gdlr-core-column-extend-left">
                    <div class="gdlr-core-pbf-sidebar-content-inner">

                        <!-- Main image (use first image if available, otherwise room_image field) -->
                        <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first">
                            <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                                <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                    <div class="gdlr-core-pbf-element">
                                        <div class="gdlr-core-image-item gdlr-core-item-pdb gdlr-core-center-align gdlr-core-item-pdlr">
                                            <div class="gdlr-core-image-item-wrap gdlr-core-media-image gdlr-core-image-item-style-round" style="border-radius:20px;">
                                                <?php
                                                    $main_img = '';
                                                    if (!empty($room_images) && !empty($room_images[0]['image_url'])) {
                                                        $main_img = $room_images[0]['image_url'];
                                                    } else if (!empty($room['room_image'])) {
                                                        $main_img = $room['room_image'];
                                                    }
                                                ?>
                                                <?php if ($main_img): ?>
                                                    <img src="<?php echo esc($main_img); ?>" alt="<?php echo esc($room['title'] ?? ''); ?>" style="width:100%; height:auto; object-fit:cover; border-radius:12px;" />
                                                <?php else: ?>
                                                    <div style="width:100%;height:380px;background:#f0f0f0;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#999;">
                                                        No Image Available
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Title, price, rating summary -->
                        <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-1">
                            <div class="gdlr-core-pbf-column-content-margin" style="padding:10px 0 20px 0;">
                                <div class="tourmaster-room-title-item tourmaster-item-mglr tourmaster-item-pdb clearfix" style="padding-bottom: 35px;">
                                    <h3 class="tourmaster-room-title-item-title"><?php echo esc($room['title'] ?? $room['room_name'] ?? 'Room'); ?></h3>
                                    <div class="tourmaster-room-title-item-caption">Room Features</div>
                                    <div class="tourmaster-room-title-price" style="margin-top:10px;">
                                        <div class="tourmaster-head">
                                            <span class="tourmaster-label">From</span>
                                            <span class="tourmaster-price">$<?php echo esc($room['price'] ?? '0'); ?></span>
                                        </div>
                                        <div class="tourmaster-tail">per night</div>
                                    </div>

                                    <div id="rating-summary" style="margin-top:12px; font-size:16px;">
                                        <strong>Average Rating:</strong>
                                        <span id="avg-rating"><?php echo esc($avgRating); ?></span> ★
                                        (<span id="total-ratings"><?php echo esc($totalRatings); ?></span> reviews)
                                    </div>
                                </div>

                                <div class="gdlr-core-divider-item"><div class="gdlr-core-divider-line gdlr-core-skin-divider"></div></div>
                            </div>
                        </div>

                        <!-- Meta: bed, guests, room space -->
                        <div class="gdlr-core-pbf-column gdlr-core-column-20 gdlr-core-column-first" id="gdlr-core-column-2">
                            <div class="gdlr-core-column-service-item" style="padding-bottom:20px;">
                                <div class="gdlr-core-column-service-media gdlr-core-media-icon">
                                    <i class="gdlr-icon-double-bed2" style="font-size:33px;"></i>
                                </div>
                                <div class="gdlr-core-column-service-content-wrapper">
                                    <h3 class="gdlr-core-column-service-title">Bed</h3>
                                    <div class="gdlr-core-column-service-caption"><?php echo esc($room['bed_size'] ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="gdlr-core-pbf-column gdlr-core-column-20" id="gdlr-core-column-3">
                            <div class="gdlr-core-column-service-item" style="padding-bottom:20px;">
                                <div class="gdlr-core-column-service-media"><i class="gdlr-icon-group" style="font-size:40px;"></i></div>
                                <div class="gdlr-core-column-service-content-wrapper">
                                    <h3 class="gdlr-core-column-service-title">Max Guest</h3>
                                    <div class="gdlr-core-column-service-caption"><?php echo esc($room['guest_capacity'] ?? 'N/A'); ?> Guests</div>
                                </div>
                            </div>
                        </div>

                        <div class="gdlr-core-pbf-column gdlr-core-column-20" id="gdlr-core-column-4">
                            <div class="gdlr-core-column-service-item" style="padding-bottom:20px;">
                                <div class="gdlr-core-column-service-media"><i class="gdlr-icon-resize" style="font-size:34px;"></i></div>
                                <div class="gdlr-core-column-service-content-wrapper">
                                    <h3 class="gdlr-core-column-service-title">Room Space</h3>
                                    <div class="gdlr-core-column-service-caption"><?php echo esc($room['room_space'] ?? 'N/A'); ?> sqm.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-6">
                            <div class="gdlr-core-text-box-item">
                                <div class="gdlr-core-text-box-item-content" style="font-size:18px;">
                                    <?php echo nl2br(esc($room['description'] ?? '')); ?>
                                </div>
                            </div>
                        </div>

                        <div class="gdlr-core-pbf-column gdlr-core-column-60">
                            <div class="gdlr-core-title-item" style="padding: 30px 0 10px 0;">
                                <h3 class="gdlr-core-title-item-title">Room Amenities</h3>
                            </div>
                        </div>

                        <!-- Amenities grid -->
                        <div class="row">
                            <?php foreach ($room_amenities as $index => $amenity): ?>
                                <?php if ($index % 3 == 0 && $index > 0): ?>
                                    </div><div class="row">
                                <?php endif; ?>
                                <div class="col-6 col-md-4 mb-3">
                                    <div class="p-3 border rounded bg-light text-center text-uppercase text-dark" style="font-size:22px;">
                                        <?php echo esc($amenity['facility_name'] ?? ''); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>

                <!-- Sidebar: Enquiry + Rating -->
                <div class="gdlr-core-pbf-sidebar-right gdlr-core-column-extend-right hotale-sidebar-area gdlr-core-column-15 gdlr-core-pbf-sidebar-padding gdlr-core-line-height">
                    <div class="gdlr-core-sidebar-item gdlr-core-item-pdlr">
                        <div class="tourmaster-room-booking-bar-wrap">
                            <div class="tourmaster-room-booking-bar-title"><span class="tourmaster-active" data-room-tab="booking">Enquiry</span></div>
                            <div class="tourmaster-room-booking-bar-content">
                                <div class="tourmaster-room-booking-wrap" data-room-tab="booking">

                                    <?php if ($user): ?>
                                        <form id="enquiry-form" method="post" style="margin-bottom:0;">
                                            <div class="tourmaster-room-date-selection tourmaster-vertical">
                                                <div class="tourmaster-custom-start-date gdlr-core-skin-e-background">
                                                    <div class="tourmaster-head gdlr-core-skin-e-content">Your ID</div>
                                                    <div class="tourmaster-tail gdlr-core-skin-e-content"><?php echo esc($user['user_id']); ?></div>
                                                </div>

                                                <div class="tourmaster-custom-message gdlr-core-skin-e-background" style="margin-top:10px;">
                                                    <div class="tourmaster-head gdlr-core-skin-e-content">Message</div>
                                                    <div class="tourmaster-tail gdlr-core-skin-e-content">
                                                        <textarea id="enquiry-text" name="message" rows="4" placeholder="Enter your message..." required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="room_id" value="<?php echo esc($room['room_id']); ?>">
                                            <input type="hidden" name="action" value="submit_enquiry">

                                            <button type="submit" class="tourmaster-room-button tourmaster-full" style="margin-top:10px;padding:10px 12px;">Send Enquiry</button>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <p>Please <a href="login.php">login</a> first to send an enquiry.</p>
                                        </div>
                                    <?php endif; ?>

                                    <div id="enquiry-result" style="margin-top:10px;"></div>

                                    <!-- Rating block -->
                                    <div style="margin-top:20px;padding-top:10px;border-top:1px dashed #e5e5e5;">
                                        <h4 style="margin:0 0 8px 0;">Rate this stay</h4>

                                        <?php if ($user): ?>
                                            <form id="rating-form">
                                                <input type="hidden" name="room_id" value="<?php echo esc($room['room_id']); ?>">
                                                <input type="hidden" name="action" value="submit_rating">

                                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                                    <label for="rating-select" style="min-width:80px;">Your Rating</label>
                                                    <select id="rating-select" name="rating" required style="padding:6px;">
                                                        <option value="">-- Select --</option>
                                                        <?php for ($i=1; $i<=5; $i++): ?>
                                                            <option value="<?php echo $i; ?>" <?php echo ($userRating === $i) ? 'selected' : ''; ?>><?php echo $i; ?> ★</option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div style="display:flex;gap:8px;">
                                                    <button type="submit" class="tourmaster-room-button" style="padding:8px 12px;">Submit Rating</button>
                                                    <button type="button" id="clear-rating" class="tourmaster-room-button" style="padding:8px 12px;background:#f2f2f2;border:1px solid #ddd;">Clear</button>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <div class="alert alert-warning">Please <a href="login.php">login</a> to rate this stay.</div>
                                        <?php endif; ?>

                                        <div id="rating-result" style="margin-top:8px;"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end sidebar -->

            </div>
        </div>

        <!-- Gallery: Clickable thumbnails -->
        <div class="gdlr-core-pbf-wrapper" id="gdlr-core-wrapper-2" style="margin-top:30px;">
            <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-pbf-wrapper-full">
                    <div class="gdlr-core-pbf-element">
                        <div class="gdlr-core-gallery-item gdlr-core-item-pdb clearfix gdlr-core-gallery-item-style-scroll gdlr-core-item-pdlr">
                            <div class="gdlr-core-sly-slider gdlr-core-js-2">
                                <ul class="slides" style="list-style:none;padding:0;display:flex;gap:12px;overflow:auto;">
                                    <?php if (!empty($room_images)): ?>
                                        <?php foreach ($room_images as $img): ?>
                                            <li style="min-width:250px;">
                                                <div class="gdlr-core-media-image" style="height:180px;overflow:hidden;border-radius:8px;border:1px solid #eee;">
                                                    <a class="gdlr-core-lightgallery" href="<?php echo esc($img['image_url']); ?>" target="_blank">
                                                        <img src="<?php echo esc($img['image_url']); ?>" alt="room image" style="width:100%;height:100%;object-fit:cover; display:block;" />
                                                    </a>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li>
                                            <div style="padding:20px;background:#fafafa;border:1px solid #eee;border-radius:8px;">
                                                No additional images.
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="gdlr-core-pbf-section" style="margin-top:30px;">
            <div class="gdlr-core-pbf-section-container gdlr-core-container clearfix">
                <div class="gdlr-core-divider-item"><div class="gdlr-core-divider-line"></div></div>
            </div>
        </div>

    </div> <!-- end body -->
</div> <!-- end wrapper -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enquiry AJAX
    var enquiryForm = document.getElementById('enquiry-form');
    var enquiryResult = document.getElementById('enquiry-result');
    if (enquiryForm) {
        enquiryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            enquiryResult.style.color = 'black';
            enquiryResult.innerText = 'Sending enquiry...';

            var fd = new FormData(enquiryForm);

            fetch(window.location.href, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            }).then(function(resp) {
                // If server returns HTML error page, parsing JSON will throw and go to catch
                return resp.json();
            }).then(function(json) {
                if (json.success) {
                    enquiryResult.style.color = 'green';
                    enquiryResult.innerText = json.message || 'Enquiry sent';
                    var ta = document.getElementById('enquiry-text');
                    if (ta) ta.value = '';
                } else {
                    enquiryResult.style.color = 'red';
                    enquiryResult.innerText = json.message || 'Error sending enquiry';
                }
            }).catch(function(err) {
                enquiryResult.style.color = 'red';
                // Show raw error — often contains HTML from a PHP warning. Helpful for debugging.
                enquiryResult.innerText = 'Network error: ' + err;
                console.error('Enquiry fetch error', err);
            });
        });
    }

    // Rating AJAX
    var ratingForm = document.getElementById('rating-form');
    var ratingResult = document.getElementById('rating-result');
    if (ratingForm) {
        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            ratingResult.style.color = 'black';
            ratingResult.innerText = 'Submitting rating...';

            var fd = new FormData(ratingForm);

            fetch(window.location.href, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            }).then(function(resp) {
                return resp.json();
            }).then(function(json) {
                if (json.success) {
                    ratingResult.style.color = 'green';
                    var msg = json.message || 'Rating saved';
                    if (json.avg !== undefined && json.count !== undefined) {
                        msg += ' — Average: ' + json.avg + ' ★ (' + json.count + ' reviews)';
                        // Update rating summary on the page
                        var avgElem = document.getElementById('avg-rating');
                        var totalElem = document.getElementById('total-ratings');
                        if (avgElem) avgElem.innerText = json.avg;
                        if (totalElem) totalElem.innerText = json.count;
                    }
                    ratingResult.innerText = msg;
                } else {
                    ratingResult.style.color = 'red';
                    ratingResult.innerText = json.message || 'Error saving rating';
                }
            }).catch(function(err) {
                ratingResult.style.color = 'red';
                ratingResult.innerText = 'Network error: ' + err;
                console.error('Rating fetch error', err);
            });
        });
    }

    // Clear rating button (client-side only)
    var clearBtn = document.getElementById('clear-rating');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            var sel = document.getElementById('rating-select');
            if (sel) sel.value = '';
            var out = document.getElementById('rating-result');
            if (out) {
                out.style.color = 'black';
                out.innerText = 'Rating cleared locally. To remove server-side rating, implement delete endpoint.';
            }
        });
    }
});
</script>

<?php
// Include footer and close DB
include 'includes/footer.php';

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
