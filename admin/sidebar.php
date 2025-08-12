<?php
// Usage: include this file in admin pages
$page = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="sidebar">
    <div class="logo">
        <h2><i class="fas fa-hotel"></i> StayByte</h2>
    </div>
    <nav>
        <ul>
            <li>
                <a href="dashboard.php" class="<?php echo ($page=='dashboard')?'active':''; ?>">
                    <span class="icon"><i class="fa fa-home"></i></span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="rooms.php" class="<?php echo ($page=='rooms')?'active':''; ?>">
                    <span class="icon"><i class="fa fa-bed"></i></span>
                    <span>Stay</span>
                </a>
            </li>
            <li>
                <a href="meals.php" class="<?php echo ($page=='meals')?'active':''; ?>">
                    <span class="icon"><i class="fa fa-utensils"></i></span>
                    <span>Meals</span>
                </a>
            </li>
            <li>
                <a href="enquiries.php" class="<?php echo ($page=='enquiries')?'active':''; ?>">
                    <span class="icon"><i class="fa fa-question-circle"></i></span>
                    <span>Enquiries</span>
                </a>
            </li>
            <li>
                <a href="users.php" class="<?php echo ($page=='users')?'active':''; ?>">
                    <span class="icon"><i class="fa fa-users"></i></span>
                    <span>Users</span>
                </a>
            </li>
            <li>
                <!-- news -->
                <a href="news.php" class="<?php echo ($page=='news')?'active':''; ?>">
                    <span class="icon"><i class="fa fa-newspaper"></i></span>
                    <span>News</span>
                </a>
            </li>
            <!-- events -->
            <li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <ul>
            <li>
                <a href="profile.php" class="<?php echo ($page=='profile')?'active':''; ?>">
                    <span class="icon"><i class="fa fa-user"></i></span>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <span class="icon"><i class="fa fa-sign-out-alt"></i></span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
