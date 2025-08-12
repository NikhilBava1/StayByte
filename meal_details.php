<?php
// meal_details.php
// Full meal details page with enquiry + rating (AJAX)
// Requires: includes/header.php, includes/footer.php, config/db.php (provides $conn as mysqli)

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    
}

include 'includes/header.php';
include 'config/db.php';

// --- Helper for safe output ---
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// --- Get logged-in user if present ---
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $stmtUser = $conn->prepare("SELECT user_id, username, email FROM users WHERE user_id = ?");
    $stmtUser->bind_param("i", $user_id);
    $stmtUser->execute();
    $resUser = $stmtUser->get_result();
    if ($resUser && $resUser->num_rows > 0) {
        $user = $resUser->fetch_assoc();
    }
    $stmtUser->close();
}

// --- Validate & get meal_id from query string ---
if (!isset($_GET['meal_id']) || !is_numeric($_GET['meal_id'])) {
    die("❌ Invalid Meal ID.");
}
$meal_id = intval($_GET['meal_id']);

// --- Fetch meal details ---
$stmt = $conn->prepare("SELECT * FROM meals WHERE meal_id = ?");
$stmt->bind_param("i", $meal_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    die("⚠️ Meal not found.");
}
$meal = $result->fetch_assoc();
$stmt->close();

// --- Fetch average rating & count ---
$avgRating = 0.0;
$totalRatings = 0;
$stmtAvg = $conn->prepare("SELECT COUNT(*) as cnt, AVG(rating) as avg_rating FROM rating WHERE meal_id = ?");
$stmtAvg->bind_param("i", $meal_id);
$stmtAvg->execute();
$resAvg = $stmtAvg->get_result();
if ($resAvg && $row = $resAvg->fetch_assoc()) {
    $totalRatings = intval($row['cnt']);
    $avgRating = $row['avg_rating'] !== null ? round(floatval($row['avg_rating']), 2) : 0.0;
}
$stmtAvg->close();

// --- Fetch this user's rating for this meal (if logged in) ---
$userRating = null;
if ($user) {
    $stmtUR = $conn->prepare("SELECT rating FROM rating WHERE meal_id = ? AND user_id = ? LIMIT 1");
    $stmtUR->bind_param("ii", $meal_id, $user['user_id']);
    $stmtUR->execute();
    $resUR = $stmtUR->get_result();
    if ($resUR && $resUR->num_rows > 0) {
        $userRating = intval($resUR->fetch_assoc()['rating']);
    }
    $stmtUR->close();
}

// Keep DB connection open; AJAX endpoints will be separate (submit_enquiry.php, submit_rating.php)
?>
<!-- main markup begins -->
<div class="tourmaster-room-single-header-title-wrap" style="border-radius: 20px;">
    <div class="tourmaster-room-single-header-background-overlay"></div>
    <div class="tourmaster-container">
        <h1 class="tourmaster-item-pdlr">
            <p><?php echo e($meal['meal_title'] ?? 'No Title'); ?></p>
        </h1>

        <!-- Average rating display -->
        <div style="margin-top:8px;">
            <span style="font-weight:700;">Average:</span>
            <span id="avg-score" style="margin-left:8px; font-weight:600;">
                <?php echo ($totalRatings > 0) ? $avgRating . ' / 5' : 'No ratings yet'; ?>
            </span>
            <span id="rating-count" style="color:#666; margin-left:10px;">(<?php echo $totalRatings; ?>)</span>
        </div>

    </div>
</div>

<div class="gdlr-core-page-builder-body">
    <div class="gdlr-core-pbf-sidebar-wrapper gdlr-core-sticky-sidebar gdlr-core-js" id="gdlr-core-sidebar-wrapper-1">
        <div class="gdlr-core-pbf-sidebar-container gdlr-core-line-height-0 clearfix gdlr-core-js gdlr-core-container">
            <div class="gdlr-core-pbf-sidebar-content gdlr-core-column-45 gdlr-core-pbf-sidebar-padding gdlr-core-line-height gdlr-core-column-extend-left">
                <div class="gdlr-core-pbf-sidebar-content-inner">

                    <!-- Meal Image -->
                    <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first">
                        <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                            <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                <div class="gdlr-core-pbf-element">
                                    <div class="gdlr-core-image-item gdlr-core-item-pdb gdlr-core-center-align gdlr-core-item-pdlr">
                                        <div class="gdlr-core-image-item-wrap gdlr-core-media-image gdlr-core-image-item-style-round" style="border-width: 0px; border-radius: 20px;">
                                            <img
                                                src="<?php echo e($meal['image_url']); ?>"
                                                alt="<?php echo e($meal['meal_title']); ?>"
                                                width="1150"
                                                height="490"
                                                title="<?php echo e($meal['meal_title']); ?>"
                                                style="width:100%; height:auto; object-fit:cover; border-radius:12px;"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meal Title and Price -->
                    <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-1">
                        <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="margin: 0; padding: 10px 0 20px 0;">
                            <div class="gdlr-core-pbf-background-wrap"></div>
                            <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                <div class="gdlr-core-pbf-element">
                                    <div class="tourmaster-room-title-item tourmaster-item-mglr tourmaster-item-pdb clearfix" style="padding-bottom: 35px;">
                                        <h3 class="tourmaster-room-title-item-title"><?php echo e($meal['meal_title']); ?></h3>
                                        <div class="tourmaster-room-title-caption">Meal Features</div>
                                        <div class="tourmaster-room-title-price">
                                            <div class="tourmaster-head">
                                                <span class="tourmaster-label">From</span>
                                                <span class="tourmaster-price">$<?php echo e($meal['price'] ?? 'N/A'); ?></span>
                                            </div>
                                            <div class="tourmaster-tail">per serving</div>
                                        </div>
                                    </div>
                                    <div id="rating-summary" style="margin-top:12px; font-size:16px;">
                                        <strong>Average Rating:</strong>
                                        <span id="avg-rating"><?php echo esc($avgRating); ?></span> ★
                                        (<span id="total-ratings"><?php echo esc($totalRatings); ?></span> reviews)
                                    </div>
                                </div>
                                <div class="gdlr-core-pbf-element">
                                    <div class="gdlr-core-divider-item gdlr-core-divider-item-normal gdlr-core-item-pdlr gdlr-core-center-align">
                                        <div class="gdlr-core-divider-line gdlr-core-skin-divider"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Ingredient -->
                    <div class="gdlr-core-pbf-column gdlr-core-column-20 gdlr-core-column-first" id="gdlr-core-column-2">
                        <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                            <div class="gdlr-core-pbf-background-wrap"></div>
                            <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                <div class="gdlr-core-pbf-element">
                                    <div class="gdlr-core-column-service-item gdlr-core-item-pdb gdlr-core-left-align gdlr-core-column-service-icon-left gdlr-core-with-caption gdlr-core-item-pdlr" style="padding-bottom: 20px;">
                                        <div class="gdlr-core-column-service-media gdlr-core-media-icon">
                                            <i class="gdlr-icon-food" style="font-size: 33px; line-height: 33px; width: 33px; color: #0a0a0a;"></i>
                                        </div>
                                        <div class="gdlr-core-column-service-content-wrapper">
                                            <div class="gdlr-core-column-service-title-wrap">
                                                <h3 class="gdlr-core-column-service-title gdlr-core-skin-title" style="font-size: 19px; font-weight: 600; text-transform: none;">Main Ingredient</h3>
                                                <div class="gdlr-core-column-service-caption gdlr-core-info-font gdlr-core-skin-caption" style="font-size: 17px; font-weight: 500; margin-top: 0;">
                                                    <p><?php echo nl2br(e($meal['main_ingredient'] ?? 'Not specified')); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Serves -->
                    <div class="gdlr-core-pbf-column gdlr-core-column-20" id="gdlr-core-column-3">
                        <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                            <div class="gdlr-core-pbf-background-wrap"></div>
                            <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                <div class="gdlr-core-pbf-element">
                                    <div class="gdlr-core-column-service-item gdlr-core-item-pdb gdlr-core-left-align gdlr-core-column-service-icon-left gdlr-core-with-caption gdlr-core-item-pdlr" style="padding-bottom: 20px;">
                                        <div class="gdlr-core-column-service-media gdlr-core-media-icon" style="margin-top: 0; margin-right: 26px;">
                                            <i class="gdlr-icon-group" style="font-size: 40px; line-height: 40px; width: 40px; color: #0a0a0a;"></i>
                                        </div>
                                        <div class="gdlr-core-column-service-content-wrapper">
                                            <div class="gdlr-core-column-service-title-wrap">
                                                <h3 class="gdlr-core-column-service-title gdlr-core-skin-title" style="font-size: 19px; font-weight: 600; text-transform: none;">Serves</h3>
                                                <div class="gdlr-core-column-service-caption gdlr-core-info-font gdlr-core-skin-caption" style="font-size: 17px; font-weight: 500; margin-top: 0;">
                                                    <?php echo e($meal['serves'] ?? 'N/A'); ?> People
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- spacing columns kept intentionally -->
                    <div class="gdlr-core-pbf-column gdlr-core-column-20">
                        <div class="gdlr-core-pbf-column-content-margin gdlr-core-js">
                            <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js"></div>
                        </div>
                    </div>

                    <!-- divider -->
                    <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-7">
                        <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="margin: 0; padding: 0 0 25px 0;">
                            <div class="gdlr-core-pbf-background-wrap"></div>
                            <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                                <div class="gdlr-core-pbf-element">
                                    <div class="gdlr-core-divider-item gdlr-core-divider-item-normal gdlr-core-item-pdlr gdlr-core-center-align">
                                        <div class="gdlr-core-divider-line gdlr-core-skin-divider"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Sidebar Enquiry & Rating (kept structure similar to yours) -->
            <div class="gdlr-core-pbf-sidebar-right gdlr-core-column-extend-right hotale-sidebar-area gdlr-core-column-15 gdlr-core-pbf-sidebar-padding gdlr-core-line-height">
                <div class="gdlr-core-sidebar-item gdlr-core-item-pdlr">
                    <div class="tourmaster-room-booking-bar-wrap">
                        <div class="tourmaster-room-booking-bar-title"><span class="tourmaster-active" data-room-tab="booking">Enquiry</span></div>
                        <div class="tourmaster-room-booking-bar-content">
                            <div class="tourmaster-room-booking-wrap" data-room-tab="booking">

                                <?php if ($user): ?>
                                <form
                                    class="tourmaster-room-booking-form clearfix"
                                    id="tourmaster-room-booking-form"
                                    action="#"
                                    data-action="tourmaster_room_booking_form"
                                    method="POST"
                                >
                                    <div class="tourmaster-room-date-selection tourmaster-vertical">
                                        <div class="tourmaster-custom-start-date gdlr-core-skin-e-background">
                                            <div class="tourmaster-head gdlr-core-skin-e-content">Your ID</div>
                                            <div class="tourmaster-tail gdlr-core-skin-e-content"><?php echo e($user['user_id']); ?></div>
                                            <input type="hidden" name="start_date" value="2022-07-28" />
                                        </div>

                                        <!-- enquiry context/message -->
                                        <div class="tourmaster-custom-message gdlr-core-skin-e-background">
                                            <div class="tourmaster-head gdlr-core-skin-e-content">Your Message</div>
                                            <div class="tourmaster-tail gdlr-core-skin-e-content">
                                                <textarea name="message" id="enquiry-message-text" rows="4" placeholder="Enter your message here..." data-required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; resize: vertical;"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="user_id" value="<?php echo e($user['user_id']); ?>" />
                                    <input type="hidden" name="room_id" value="<?php echo e($meal['meal_id']); ?>" />
                                    <input type="hidden" name="category" value="meal" />
                                    <input type="hidden" name="post_type" value="meal" />
                                    <input type="submit" class="tourmaster-room-button tourmaster-full" value="Enquiry" />
                                </form>
                                <?php else: ?>
                                <div class="alert alert-warning">
                                    <p>Please <a href="login.php">login</a> first to send an enquiry.</p>
                                </div>
                                <?php endif; ?>

                                <div id="enquiry-feedback" style="margin-top: 10px;"></div>

                                <!-- Rating block (below enquiry) - UPDATED RATING UI -->
                                <div style="margin-top:20px; padding-top:10px; border-top:1px dashed #e5e5e5;">
                                    <h4 style="margin:0 0 8px 0;">Rate this Meal</h4>

                                    <?php if ($user): ?>
                                    <form id="rating-form" style="display:flex; flex-direction:column; gap:8px;">
                                        <input type="hidden" name="meal_id" id="rating-meal-id" value="<?php echo e($meal['meal_id']); ?>">
                                        <input type="hidden" name="user_id" id="rating-user-id" value="<?php echo e($user['user_id']); ?>">

                                        <!-- star-like selectable UI using radio buttons (kept simple with labels) -->
                                        <div id="rating-stars" style="display:flex; align-items:center; gap:8px; font-size:22px;">
                                            <?php
                                            // show star radios & labels; this allows keyboard accessibility too
                                            for ($i = 5; $i >= 1; $i--) {
                                                $checked = ($userRating === $i) ? 'checked' : '';
                                                echo '<input type="radio" id="star' . $i . '" name="rating" value="' . $i . '" ' . $checked . ' style="display:none;">';
                                                echo '<label for="star' . $i . '" title="' . $i . ' stars" data-value="' . $i . '" style="cursor:pointer; color:' . ($userRating && $userRating >= $i ? '#ffb400' : '#ddd') . '">&#9733;</label>';
                                            }
                                            ?>
                                        </div>

                                        <div style="display:flex; align-items:center; gap:8px; margin-top:6px;">
                                            <button type="submit" class="tourmaster-room-button" style="padding:8px 12px;">Submit Rating</button>
                                            <button type="button" id="clear-rating" class="tourmaster-room-button" style="padding:8px 12px; background:#f2f2f2; color:#333;">Clear</button>
                                            <div id="rating-current" style="margin-left:auto; color:#444; font-weight:600;"><?php echo ($userRating ? "Your: {$userRating}★" : "You haven't rated"); ?></div>
                                        </div>

                                    </form>
                                    <?php else: ?>
                                        <div class="alert alert-warning">Please <a href="login.php">login</a> to rate this meal.</div>
                                    <?php endif; ?>

                                    <div id="rating-feedback" style="margin-top:8px;"></div>
                                </div>
                                <!-- END rating block -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- lower divider section -->
        <div class="gdlr-core-pbf-section">
            <div class="gdlr-core-pbf-section-container gdlr-core-container clearfix">
                <div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first" id="gdlr-core-column-23">
                    <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="margin: 0; padding: 30px 0 10px 0;">
                        <div class="gdlr-core-pbf-background-wrap"></div>
                        <div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
                            <div class="gdlr-core-pbf-element">
                                <div class="gdlr-core-divider-item gdlr-core-divider-item-normal gdlr-core-item-pdlr gdlr-core-center-align" style="margin-bottom: 20px;">
                                    <div class="gdlr-core-divider-line gdlr-core-skin-divider"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- jQuery CDN (optional; adjust if you already load it via header) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
/*
 Client-side JS for:
  - Submitting enquiry (AJAX to submit_enquiry.php)
  - Submitting rating (AJAX to submit_rating.php)
  - Star UI interactivity and feedback.
*/

// Wait for DOM
$(function() {

    // --- Enquiry form submit (AJAX) ---
    $('#tourmaster-room-booking-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var msg = $.trim($('#enquiry-message-text').val());

        if (!msg) {
            $('#enquiry-feedback').text('Please enter a message.').css('color', 'red').show();
            return;
        }

        $('#enquiry-feedback').text('Sending enquiry...').css('color', 'blue').show();

        // send to your endpoint (create submit_enquiry.php to accept these fields)
        $.ajax({
            url: 'submit_enquiry.php',
            method: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function(resp) {
                if (resp.success) {
                    $('#enquiry-feedback').text(resp.message || 'Enquiry sent.').css('color', 'green');
                    $('#enquiry-message-text').val('');
                } else {
                    $('#enquiry-feedback').text(resp.message || 'Failed to send enquiry.').css('color', 'red');
                }
            },
            error: function(xhr, status, err) {
                $('#enquiry-feedback').text('Server error sending enquiry.').css('color', 'red');
            }
        });
    });

    // --- Rating star hover & click behavior ---
    $('#rating-stars label').on('mouseenter', function() {
        var idx = $(this).data('value');
        $('#rating-stars label').each(function() {
            $(this).css('color', $(this).data('value') <= idx ? '#ffb400' : '#ddd');
        });
    }).on('mouseleave', function() {
        resetStars();
    });

    // When user clicks a star label, check the radio
    $('#rating-stars label').on('click', function() {
        var v = $(this).data('value');
        $('#star' + v).prop('checked', true);
        $('#rating-current').text('Your: ' + v + '★');
        // keep stars highlighted
        resetStars();
    });

    function resetStars() {
        var checked = $('#rating-form input[name="rating"]:checked').val();
        if (checked) {
            $('#rating-stars label').each(function() {
                $(this).css('color', $(this).data('value') <= checked ? '#ffb400' : '#ddd');
            });
        } else {
            $('#rating-stars label').css('color', '#ddd');
            $('#rating-current').text("You haven't rated");
        }
    }

    resetStars();

    // --- Clear rating action ---
    $('#clear-rating').on('click', function() {
        $('#rating-form input[name="rating"]').prop('checked', false);
        resetStars();
        $('#rating-feedback').text('').hide();
    });

    // --- Rating form submit (AJAX) ---
    $('#rating-form').on('submit', function(e) {
        e.preventDefault();

        var meal_id = $('#rating-meal-id').val();
        var rating = $('#rating-form input[name="rating"]:checked').val();

        if (!rating) {
            $('#rating-feedback').text('Please select a rating.').css('color', 'red').show();
            return;
        }

        $('#rating-feedback').text('Submitting rating...').css('color', 'blue').show();

        $.ajax({
            url: 'submit_rating.php',
            method: 'POST',
            dataType: 'json',
            data: { meal_id: meal_id, rating: rating },
            success: function(resp) {
                if (resp.success) {
                    $('#rating-feedback').text(resp.message || 'Rating saved.').css('color', 'green');
                    // update displayed average & count
                    if (typeof resp.avg !== 'undefined') {
                        $('#avg-score').text(resp.avg + ' / 5');
                        $('#rating-count').text('(' + resp.count + ')');
                    }
                    // update current user rating display
                    $('#rating-current').text('Your: ' + rating + '★');
                    resetStars();
                } else {
                    $('#rating-feedback').text(resp.message || 'Failed to save rating.').css('color', 'red');
                }
            },
            error: function() {
                $('#rating-feedback').text('Server error submitting rating.').css('color', 'red');
            }
        });
    });

});
</script>

<!-- end of meal_details.php -->
