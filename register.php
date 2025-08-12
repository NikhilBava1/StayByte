<?php   
include 'includes/header.php';
include 'config/db.php';

$error_message = '';
$success_message = '';

// Fetch roles for dropdown
$sql = "SELECT * FROM user_roles ORDER BY role_name";
$result = mysqli_query($conn, $sql);
$roles = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $country = $_POST['country'];
    $role_id = (int)$_POST['role_id'];
    $birth_day = $_POST['birth_day'];
    $birth_month = $_POST['birth_month'];
    $birth_year = $_POST['birth_year'];
    
    // Combine birth date
    $birth_date = '';
    if (!empty($birth_day) && !empty($birth_month) && !empty($birth_year)) {
        $birth_date = $birth_year . '-' . str_pad($birth_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($birth_day, 2, '0', STR_PAD_LEFT);
    }
    
    // Validation
    if (empty($username) || empty($password) || empty($confirm_password) || empty($first_name) || 
        empty($last_name) || empty($email) || empty($phone) || empty($role_id) || empty($birth_day) || empty($birth_month) || empty($birth_year)) {
        $error_message = 'All fields are required!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match!';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long!';
    } else {
        // Check if username already exists
        $sql = "SELECT user_id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);        
        
        if (mysqli_num_rows($result) > 0) {
            $error_message = 'Username already exists!';
        } else {
            // Check if email already exists
            $sql = "SELECT user_id FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                $error_message = 'Email already exists!';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user
                $sql = "INSERT INTO users (username, email, first_name, last_name, phone, country, birth_date, password, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssssssss", $username, $email, $first_name, $last_name, $phone, $country, $birth_date, $hashed_password, $role_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $id = mysqli_insert_id($conn);
                    
                    // If role is student, create student record
                    
                    
                    $success_message = 'Registration successful! You can now login.';
                    
                    // Clear form data
                    $username = $password = $confirm_password = $first_name = $last_name = $email = $phone = '';
                } else {
                    $error_message = 'Registration failed! Please try again.';
                }
            }
        }
    }
}

mysqli_close($conn);
?>  

<div class="hotale-page-title-wrap hotale-style-custom hotale-center-align">
    <div class="hotale-header-transparent-substitute"></div>
    <div class="hotale-page-title-overlay"></div>
    <div class="hotale-page-title-container hotale-container">
        <div class="hotale-page-title-content hotale-item-pdlr"><h1 class="hotale-page-title">Register</h1></div>
    </div>
</div>
<div class="hotale-page-wrapper" id="hotale-page-wrapper">
    <div class="tourmaster-template-wrapper">
        <div class="tourmaster-container">
            <div class="tourmaster-page-content tourmaster-item-pdlr">
                
                <?php if (!empty($error_message)): ?>
                    <div class="tourmaster-notification-box tourmaster-failure" style="margin-bottom: 20px;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="tourmaster-notification-box tourmaster-success" style="margin-bottom: 20px;">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <form class="tourmaster-register-form tourmaster-form-field tourmaster-with-border" action="" method="post">
                    <div class="tourmaster-register-message">
                        After creating an account, you'll be able to track your payment status, track the confirmation and you can also rate the tour after you finished the tour.
                    </div>
                    <div class="tourmaster-register-form-fields clearfix">
                        <div class="tourmaster-profile-field tourmaster-profile-field-username tourmaster-type-text clearfix">
                            <div class="tourmaster-head">Username<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" data-required />
                            </div>
                        </div>
                        <div class="tourmaster-profile-field tourmaster-profile-field-password tourmaster-type-password clearfix">
                            <div class="tourmaster-head">Password<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <input type="password" name="password" value="" data-required />
                            </div>
                        </div>
                        <div class="tourmaster-profile-field tourmaster-profile-field-confirm-password tourmaster-type-password clearfix">
                            <div class="tourmaster-head">Confirm Password<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <input type="password" name="confirm-password" value="" data-required />
                            </div>
                        </div>
                        <div class="tourmaster-profile-field tourmaster-profile-field-first_name tourmaster-type-text clearfix">
                            <div class="tourmaster-head">First Name<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <input type="text" name="first_name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" data-required />
                            </div>
                        </div>
                        <div class="tourmaster-profile-field tourmaster-profile-field-last_name tourmaster-type-text clearfix">
                            <div class="tourmaster-head">Last Name<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <input type="text" name="last_name" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" data-required />
                            </div>
                        </div>
                        <div class="tourmaster-profile-field tourmaster-profile-field-birth_date tourmaster-type-date clearfix">
                            <div class="tourmaster-head">Birth Date<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <div class="tourmaster-date-select">
                                    <div class="tourmaster-combobox-wrap tourmaster-form-field-alt-date">
                                        <select name="birth_day" data-type="date" data-required>
                                            <option value="">Day</option>
                                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo (isset($_POST['birth_day']) && $_POST['birth_day'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="tourmaster-combobox-wrap tourmaster-form-field-alt-month">
                                        <select name="birth_month" data-type="month" data-required>
                                            <option value="">Month</option>
                                            <option value="1" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '1') ? 'selected' : ''; ?>>January</option>
                                            <option value="2" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '2') ? 'selected' : ''; ?>>February</option>
                                            <option value="3" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '3') ? 'selected' : ''; ?>>March</option>
                                            <option value="4" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '4') ? 'selected' : ''; ?>>April</option>
                                            <option value="5" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '5') ? 'selected' : ''; ?>>May</option>
                                            <option value="6" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '6') ? 'selected' : ''; ?>>June</option>
                                            <option value="7" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '7') ? 'selected' : ''; ?>>July</option>
                                            <option value="8" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '8') ? 'selected' : ''; ?>>August</option>
                                            <option value="9" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '9') ? 'selected' : ''; ?>>September</option>
                                            <option value="10" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '10') ? 'selected' : ''; ?>>October</option>
                                            <option value="11" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '11') ? 'selected' : ''; ?>>November</option>
                                            <option value="12" <?php echo (isset($_POST['birth_month']) && $_POST['birth_month'] == '12') ? 'selected' : ''; ?>>December</option>
                                        </select>
                                    </div>
                                    <div class="tourmaster-combobox-wrap tourmaster-form-field-alt-year">
                                        <select name="birth_year" data-type="year" data-required>
                                            <option value="">Year</option>
                                            <?php for ($year = date('Y'); $year >= date('Y') - 100; $year--): ?>
                                                <option value="<?php echo $year; ?>" <?php echo (isset($_POST['birth_year']) && $_POST['birth_year'] == $year) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="birth_date" value="" />
                            </div>
                        </div>
                        <div class="tourmaster-profile-field tourmaster-profile-field-email tourmaster-type-email clearfix">
                            <div class="tourmaster-head">Email<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" data-required />
                            </div>
                        </div>
                        <div class="tourmaster-profile-field tourmaster-profile-field-phone tourmaster-type-text clearfix">
                            <div class="tourmaster-head">Phone<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <input type="text" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" data-required />
                            </div>
                        </div>
                        <div class="tourmaster-profile-field tourmaster-profile-field-role tourmaster-type-combobox clearfix">
                            <div class="tourmaster-head">User Role<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <div class="tourmaster-combobox-wrap">
                                    <select name="role_id" data-required>
                                        <option value="">Select Role</option>
                                        <?php foreach ($roles as $role): ?>
                                            <?php if ($role['role_id'] != 1): // Exclude admin role ?>
                                                <option value="<?php echo $role['role_id']; ?>" <?php echo (isset($_POST['role_id']) && $_POST['role_id'] == $role['role_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars(ucfirst($role['role_name'])); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tourmaster-profile-field tourmaster-profile-field-country tourmaster-type-combobox clearfix">
                            <div class="tourmaster-head">Country<span class="tourmaster-req">*</span></div>
                            <div class="tourmaster-tail clearfix">
                                <div class="tourmaster-combobox-wrap">
                                    <select name="country" data-required>
                                        <option value="">Select Country</option>
                                        <option value="India" <?php echo (isset($_POST['country']) && $_POST['country'] == 'India') ? 'selected' : ''; ?>>India</option>
                                        <option value="United States of America (USA)" <?php echo (isset($_POST['country']) && $_POST['country'] == 'United States of America (USA)') ? 'selected' : ''; ?>>United States of America (USA)</option>
                                        <option value="United Kingdom (UK)" <?php echo (isset($_POST['country']) && $_POST['country'] == 'United Kingdom (UK)') ? 'selected' : ''; ?>>United Kingdom (UK)</option>
                                        <option value="Canada" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Canada') ? 'selected' : ''; ?>>Canada</option>
                                        <option value="Australia" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Australia') ? 'selected' : ''; ?>>Australia</option>
                                        <option value="Germany" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Germany') ? 'selected' : ''; ?>>Germany</option>
                                        <option value="France" <?php echo (isset($_POST['country']) && $_POST['country'] == 'France') ? 'selected' : ''; ?>>France</option>
                                        <option value="Japan" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Japan') ? 'selected' : ''; ?>>Japan</option>
                                        <option value="China" <?php echo (isset($_POST['country']) && $_POST['country'] == 'China') ? 'selected' : ''; ?>>China</option>
                                        <option value="Brazil" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Brazil') ? 'selected' : ''; ?>>Brazil</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="redirect" value="" />
                    <div class="tourmaster-register-term">
                        <input type="checkbox" name="tourmaster-require-acceptance" required />* Creating an account means you're okay with our <a href="#" target="_blank">Terms of Service</a> and
                        <a href="#" target="_blank">Privacy Statement</a>.
                        <div class="tourmaster-notification-box tourmaster-failure">Please agree to all the terms and conditions before proceeding to the next step</div>
                    </div>
                    <input type="submit" class="tourmaster-register-submit tourmaster-button" value="Sign Up" />
                </form>
                <div class="tourmaster-register-bottom">
                    <h3 class="tourmaster-register-bottom-title">Already a member?</h3>
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
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>