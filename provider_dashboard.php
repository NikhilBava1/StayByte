<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    echo '<script>window.location.href="index.php";</script>';
    exit();
}

include 'config/db.php';

// Get provider details
$provider_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);    
mysqli_stmt_bind_param($stmt, "i", $provider_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$provider = mysqli_fetch_assoc($result);

// Fetch provider's rooms
$rooms_sql = "SELECT * FROM rooms WHERE room_provider_id = ?";
$rooms_stmt = mysqli_prepare($conn, $rooms_sql);
mysqli_stmt_bind_param($rooms_stmt, "i", $provider_id);
mysqli_stmt_execute($rooms_stmt);
$rooms_result = mysqli_stmt_get_result($rooms_stmt);
$rooms = [];
while ($row = mysqli_fetch_assoc($rooms_result)) {
    $rooms[] = $row;
}

// Fetch provider's meals
$meals_sql = "SELECT * FROM meals WHERE meal_provider_id = ?";
$meals_stmt = mysqli_prepare($conn, $meals_sql);
mysqli_stmt_bind_param($meals_stmt, "i", $provider_id);
mysqli_stmt_execute($meals_stmt);
$meals_result = mysqli_stmt_get_result($meals_stmt);
$meals = [];
while ($row = mysqli_fetch_assoc($meals_result)) {
    $meals[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard - StayByte</title>
    <link rel="stylesheet" href="css/style-core.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/hotale-style-customafa1.css" type="text/css" media="all" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:400,500,600,700&display=swap"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #f6f7fa 0%, #e9ebf0 100%);
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            color: #181818;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #dc3545;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .dashboard-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
        }
        .dashboard-card h3 {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-btn:hover {
            background: #c82333;
        }
        .action-btn {
            display: inline-block;
            padding: 8px 15px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin: 5px;
            font-size: 0.9rem;
        }
        .action-btn:hover {
            background: #c82333;
        }
        .add-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .add-btn:hover {
            background: #218838;
        }
        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .dashboard-table th, .dashboard-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .dashboard-table th {
            background-color: #f8f9fa;
            color: #333;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #ccc;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        /* Toast Notification System */
        #toast-container {
            position: fixed;
            z-index: 9999;
            bottom: 32px;
            right: 32px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }
        .toast {
            min-width: 220px;
            max-width: 340px;
            background: #232323;
            color: #fff;
            padding: 16px 24px;
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(24,24,24,0.18);
            font-size: 1.08rem;
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.4s, transform 0.4s;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            pointer-events: auto;
        }
        .toast-success { background: #1db954; color: #fff; }
        .toast-error { background: #e74c3c; color: #fff; }
        .toast-info { background: #232323; color: #fff; }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        /* Modals */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fff;
            border-radius: 10px;
            max-width: 700px;
            width: 90%;
            padding: 40px 32px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            position: relative;
        }
        .close {
            position: absolute;
            top: 18px;
            right: 18px;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #888;
            cursor: pointer;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e5e5e5;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-group label span {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        
        <div class="dashboard-header">
            <h1>Provider Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($provider['first_name'] . ' ' . $provider['last_name']); ?>!</p>
            <p><strong>Provider ID:</strong> <?php echo htmlspecialchars($provider['provider_id']); ?></p>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>🏨 My Rooms</h3>
                <p>Manage your available rooms and their details.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>🍽️ My Meals</h3>
                <p>Manage your meal services and offerings.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📊 Analytics</h3>
                <p>View performance metrics and booking statistics.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>📧 Recent Enquiries</h3>
                <p>Manage customer inquiries and messages.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>🏨 Room Bookings</h3>
                <p>View and manage room reservations.</p>
                <p><em>Feature coming soon...</em></p>
            </div>

            <div class="dashboard-card">
                <h3>🍽️ Meal Orders</h3>
                <p>View and manage meal orders.</p>
                <p><em>Feature coming soon...</em></p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="logout.php" class="action-btn">Logout</a>
        </div>
    </div>

    <!-- Large Modal for Add/Edit -->
    <div id="largeModal" class="modal">
        <div class="modal-content">
            <button class="close" onclick="closeLargeModal()"><i class="fa-solid fa-times"></i></button>
            <div id="largeModalContent">
                <!-- Form content will be injected here -->
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Delete -->
    <div id="confirmModal" class="modal">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div style="font-size: 2.2rem; color: #dc3545; margin-bottom: 12px;"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div id="confirmModalText" style="font-size: 1.1rem; color: #333; margin-bottom: 24px;">Are you sure you want to delete this item?</div>
            <button id="confirmDeleteBtn" class="action-btn" style="background: #dc3545; margin-right: 10px;">Delete</button>
            <button onclick="closeConfirmModal()" class="action-btn" style="background: #6c757d;">Cancel</button>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container"></div>

    <script>
        // Toast Notification System
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast toast-' + type;
            toast.innerHTML = `<span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => { toast.classList.add('show'); }, 10);
            setTimeout(() => { toast.classList.remove('show'); setTimeout(()=>toast.remove(), 400); }, 3500);
        }

        // Modal logic
        function showLargeModal(contentHtml) {
            document.getElementById('largeModalContent').innerHTML = contentHtml;
            document.getElementById('largeModal').style.display = 'flex';
        }

        function closeLargeModal() {
            document.getElementById('largeModal').style.display = 'none';
            document.getElementById('largeModalContent').innerHTML = '';
        }

        function showConfirmModal(text, onConfirm) {
            document.getElementById('confirmModalText').textContent = text;
            document.getElementById('confirmModal').style.display = 'flex';
            document.getElementById('confirmDeleteBtn').onclick = function() {
                onConfirm();
                closeConfirmModal();
            };
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        // Show Add Room Modal
        function showAddRoomForm() {
            showLargeModal(`
                <form id='roomForm' enctype='multipart/form-data'>
                    <h2 style='margin-bottom: 24px; font-size: 1.3rem; font-weight: 700;'>Add New Room</h2>
                    <input type='hidden' name='room_id' value=''>
                    <div class='form-group'>
                        <label>Title <span>*</span></label>
                        <input type='text' name='title' class='form-control' required />
                    </div>
                    <div class='form-group'>
                        <label>Price <span>*</span></label>
                        <input type='number' name='price' class='form-control' min='0' step='0.01' required />
                    </div>
                    <div class='form-group'>
                        <label>Status</label>
                        <select name='status' class='form-control'>
                            <option value='Active'>Active</option>
                            <option value='Inactive'>Inactive</option>
                        </select>
                    </div>
                    <div class='form-group'>
                        <label>Image</label>
                        <input type='file' name='room_image' accept='image/*' class='form-control' />
                    </div>
                    <div class='form-group'>
                        <label>Description</label>
                        <textarea name='description' class='form-control' style='min-height: 80px;'></textarea>
                    </div>
                    <button type='submit' class='add-btn' style='width: 100%; margin-top: 10px;'><i class='fa-solid fa-plus'></i> Add Room</button>
                </form>
            `);
            document.getElementById('roomForm').onsubmit = submitRoomForm;
        }

        // Show Edit Room Modal
        function editRoom(roomId) {
            // Find room data in table (or fetch via AJAX if needed)
            var row = document.querySelector('.dashboard-table tr[data-room-id="'+roomId+'"]');
            if (!row) { showToast('Room not found.', 'error'); return; }
            var cells = row.querySelectorAll('td');
            var title = cells[0].textContent.trim();
            var price = cells[1].textContent.replace('$','').trim();
            var status = cells[2].textContent.trim();
            var description = row.getAttribute('data-description') || '';
            showLargeModal(`
                <form id='roomForm' enctype='multipart/form-data'>
                    <h2 style='margin-bottom: 24px; font-size: 1.3rem; font-weight: 700;'>Edit Room</h2>
                    <input type='hidden' name='room_id' value='${roomId}'>
                    <div class='form-group'>
                        <label>Title <span>*</span></label>
                        <input type='text' name='title' value='${title}' class='form-control' required />
                    </div>
                    <div class='form-group'>
                        <label>Price <span>*</span></label>
                        <input type='number' name='price' value='${price}' class='form-control' min='0' step='0.01' required />
                    </div>
                    <div class='form-group'>
                        <label>Status</label>
                        <select name='status' class='form-control'>
                            <option value='Active' ${status==='Active'?'selected':''}>Active</option>
                            <option value='Inactive' ${status==='Inactive'?'selected':''}>Inactive</option>
                        </select>
                    </div>
                    <div class='form-group'>
                        <label>Image (leave blank to keep current)</label>
                        <input type='file' name='room_image' accept='image/*' class='form-control' />
                    </div>
                    <div class='form-group'>
                        <label>Description</label>
                        <textarea name='description' class='form-control' style='min-height: 80px;'>${description}</textarea>
                    </div>
                    <button type='submit' class='add-btn' style='width: 100%; margin-top: 10px;'><i class='fa-solid fa-save'></i> Save Changes</button>
                </form>
            `);
            document.getElementById('roomForm').onsubmit = submitRoomForm;
        }

        // Submit Room Form via AJAX
        function submitRoomForm(e) {
            e.preventDefault();
            var form = e.target;
            var formData = new FormData(form);
            fetch('add_edit_room.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    closeLargeModal();
                    // If backend returns new/updated room, update table
                    if(data.room) updateRoomRow(data.room, !!form.room_id.value);
                    showToast('Room saved successfully!', 'success');
                } else {
                    showToast(data.error || 'Failed to save room.', 'error');
                }
            })
            .catch(() => showToast('Server error. Please try again.', 'error'));
        }

        // Delete Room
        function deleteRoom(roomId) {
            showConfirmModal('Are you sure you want to delete this room?', function() {
                var formData = new FormData();
                formData.append('room_id', roomId);
                fetch('delete_room.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        removeRoomRow(roomId);
                        showToast('Room deleted.', 'success');
                    } else {
                        showToast(data.error || 'Delete failed.', 'error');
                    }
                })
                .catch(() => showToast('Server error. Please try again.', 'error'));
            });
        }

        // Show Add Meal Modal
        function showAddMealForm() {
            showLargeModal(`
                <form id='mealForm' enctype='multipart/form-data'>
                    <h2 style='margin-bottom: 24px; font-size: 1.3rem; font-weight: 700;'>Add New Meal</h2>
                    <input type='hidden' name='meal_id' value=''>
                    <div class='form-group'>
                        <label>Title <span>*</span></label>
                        <input type='text' name='meal_title' class='form-control' required />
                    </div>
                    <div class='form-group'>
                        <label>Price <span>*</span></label>
                        <input type='number' name='price' class='form-control' min='0' step='0.01' required />
                    </div>
                    <div class='form-group'>
                        <label>Status</label>
                        <select name='status' class='form-control'>
                            <option value='Active'>Active</option>
                            <option value='Inactive'>Inactive</option>
                        </select>
                    </div>
                    <div class='form-group'>
                        <label>Type</label>
                        <select name='meal_type' class='form-control'>
                            <option value='Veg'>Veg</option>
                            <option value='Non-Veg'>Non-Veg</option>
                            <option value='Jain'>Jain</option>
                        </select>
                    </div>
                    <div class='form-group'>
                        <label>Items Included</label>
                        <input type='text' name='items_included' class='form-control' />
                    </div>
                    <div class='form-group'>
                        <label>Address</label>
                        <input type='text' name='meal_address' class='form-control' />
                    </div>
                    <div class='form-group'>
                        <label>Image</label>
                        <input type='file' name='image_url' accept='image/*' class='form-control' />
                    </div>
                    <div class='form-group'>
                        <label>Description</label>
                        <textarea name='description' class='form-control' style='min-height: 80px;'></textarea>
                    </div>
                    <button type='submit' class='add-btn' style='width: 100%; margin-top: 10px;'><i class='fa-solid fa-plus'></i> Add Meal</button>
                </form>
            `);
            document.getElementById('mealForm').onsubmit = submitMealForm;
        }

        // Show Edit Meal Modal
        function editMeal(mealId) {
            var row = document.querySelector('.dashboard-table tr[data-meal-id="'+mealId+'"]');
            if (!row) { showToast('Meal not found.', 'error'); return; }
            var cells = row.querySelectorAll('td');
            var meal_title = cells[0].textContent.trim();
            var price = cells[1].textContent.replace('$','').trim();
            var status = cells[2].textContent.trim();
            var meal_type = row.getAttribute('data-meal-type') || 'Veg';
            var items_included = row.getAttribute('data-items-included') || '';
            var meal_address = row.getAttribute('data-meal-address') || '';
            var description = row.getAttribute('data-description') || '';
            showLargeModal(`
                <form id='mealForm' enctype='multipart/form-data'>
                    <h2 style='margin-bottom: 24px; font-size: 1.3rem; font-weight: 700;'>Edit Meal</h2>
                    <input type='hidden' name='meal_id' value='${mealId}'>
                    <div class='form-group'>
                        <label>Title <span>*</span></label>
                        <input type='text' name='meal_title' value='${meal_title}' class='form-control' required />
                    </div>
                    <div class='form-group'>
                        <label>Price <span>*</span></label>
                        <input type='number' name='price' value='${price}' class='form-control' min='0' step='0.01' required />
                    </div>
                    <div class='form-group'>
                        <label>Status</label>
                        <select name='status' class='form-control'>
                            <option value='Active' ${status==='Active'?'selected':''}>Active</option>
                            <option value='Inactive' ${status==='Inactive'?'selected':''}>Inactive</option>
                        </select>
                    </div>
                    <div class='form-group'>
                        <label>Type</label>
                        <select name='meal_type' class='form-control'>
                            <option value='Veg' ${meal_type==='Veg'?'selected':''}>Veg</option>
                            <option value='Non-Veg' ${meal_type==='Non-Veg'?'selected':''}>Non-Veg</option>
                            <option value='Jain' ${meal_type==='Jain'?'selected':''}>Jain</option>
                        </select>
                    </div>
                    <div class='form-group'>
                        <label>Items Included</label>
                        <input type='text' name='items_included' value='${items_included}' class='form-control' />
                    </div>
                    <div class='form-group'>
                        <label>Address</label>
                        <input type='text' name='meal_address' value='${meal_address}' class='form-control' />
                    </div>
                    <div class='form-group'>
                        <label>Image (leave blank to keep current)</label>
                        <input type='file' name='image_url' accept='image/*' class='form-control' />
                    </div>
                    <div class='form-group'>
                        <label>Description</label>
                        <textarea name='description' class='form-control' style='min-height: 80px;'>${description}</textarea>
                    </div>
                    <button type='submit' class='add-btn' style='width: 100%; margin-top: 10px;'><i class='fa-solid fa-save'></i> Save Changes</button>
                </form>
            `);
            document.getElementById('mealForm').onsubmit = submitMealForm;
        }

        // Submit Meal Form via AJAX
        function submitMealForm(e) {
            e.preventDefault();
            var form = e.target;
            var formData = new FormData(form);
            fetch('add_edit_meal.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    closeLargeModal();
                    if(data.meal) updateMealRow(data.meal, !!form.meal_id.value);
                    showToast('Meal saved successfully!', 'success');
                } else {
                    showToast(data.error || 'Failed to save meal.', 'error');
                }
            })
            .catch(() => showToast('Server error. Please try again.', 'error'));
        }

        // Delete Meal
        function deleteMeal(mealId) {
            showConfirmModal('Are you sure you want to delete this meal?', function() {
                var formData = new FormData();
                formData.append('meal_id', mealId);
                fetch('delete_meal.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        removeMealRow(mealId);
                        showToast('Meal deleted.', 'success');
                    } else {
                        showToast(data.error || 'Delete failed.', 'error');
                    }
                })
                .catch(() => showToast('Server error. Please try again.', 'error'));
            });
        }

        // Helper: update/add/remove row in table (improved for instant UI update)
        function updateRoomRow(room, isEdit) {
            let table = document.querySelector('#section-rooms .dashboard-table');
            if (!table) return;
            let row = table.querySelector('tr[data-room-id="'+room.room_id+'"]');
            if (!row) {
                // Remove empty state if present
                let empty = document.querySelector('#section-rooms .empty-state');
                if (empty) empty.remove();
                // Add new row at the top (after header)
                row = document.createElement('tr');
                row.setAttribute('data-room-id', room.room_id);
                row.setAttribute('data-description', room.description || '');
                row.innerHTML = `
                    <td>${room.title}</td>
                    <td>$${room.price}</td>
                    <td>${room.status}</td>
                    <td>
                        <button class="action-btn" onclick="editRoom(${room.room_id})"><i class="fa-solid fa-pen"></i> Edit</button>
                        <button class="action-btn" onclick="deleteRoom(${room.room_id})"><i class="fa-solid fa-trash"></i> Delete</button>
                    </td>
                `;
                let header = table.querySelector('tr');
                if (header && header.parentNode) {
                    header.parentNode.insertBefore(row, header.nextSibling);
                } else {
                    table.appendChild(row);
                }
            } else {
                // Update row
                row.querySelector('td:nth-child(1)').textContent = room.title;
                row.querySelector('td:nth-child(2)').textContent = '$'+room.price;
                row.querySelector('td:nth-child(3)').textContent = room.status;
                row.setAttribute('data-description', room.description || '');
            }
        }

        function removeRoomRow(roomId) {
            let row = document.querySelector('#section-rooms .dashboard-table tr[data-room-id="'+roomId+'"]');
            if (row) row.remove();
            // If table is now empty, show empty state
            let table = document.querySelector('#section-rooms .dashboard-table');
            if (table && table.querySelectorAll('tr[data-room-id]').length === 0) {
                let empty = document.createElement('div');
                empty.className = 'empty-state';
                empty.innerHTML = '<i class="fa-solid fa-bed"></i><div>No rooms found.<br>Click <b>+ Add New Room</b> to publish your first room.</div>';
                table.parentNode.appendChild(empty);
            }
        }

        function updateMealRow(meal, isEdit) {
            let table = document.querySelector('#section-meals .dashboard-table');
            if (!table) return;
            let row = table.querySelector('tr[data-meal-id="'+meal.meal_id+'"]');
            if (!row) {
                // Remove empty state if present
                let empty = document.querySelector('#section-meals .empty-state');
                if (empty) empty.remove();
                // Add new row at the top (after header)
                row = document.createElement('tr');
                row.setAttribute('data-meal-id', meal.meal_id);
                row.setAttribute('data-meal-type', meal.meal_type);
                row.setAttribute('data-items-included', meal.items_included);
                row.setAttribute('data-meal-address', meal.meal_address);
                row.setAttribute('data-description', meal.description || '');
                row.innerHTML = `
                    <td>${meal.meal_title}</td>
                    <td>$${meal.price}</td>
                    <td>${meal.status}</td>
                    <td>
                        <button class="action-btn" onclick="editMeal(${meal.meal_id})"><i class="fa-solid fa-pen"></i> Edit</button>
                        <button class="action-btn" onclick="deleteMeal(${meal.meal_id})"><i class="fa-solid fa-trash"></i> Delete</button>
                    </td>
                `;
                let header = table.querySelector('tr');
                if (header && header.parentNode) {
                    header.parentNode.insertBefore(row, header.nextSibling);
                } else {
                    table.appendChild(row);
                }
            } else {
                // Update row
                row.querySelector('td:nth-child(1)').textContent = meal.meal_title;
                row.querySelector('td:nth-child(2)').textContent = '$'+meal.price;
                row.querySelector('td:nth-child(3)').textContent = meal.status;
                row.setAttribute('data-meal-type', meal.meal_type);
                row.setAttribute('data-items-included', meal.items_included);
                row.setAttribute('data-meal-address', meal.meal_address);
                row.setAttribute('data-description', meal.description || '');
            }
        }

        function removeMealRow(mealId) {
            let row = document.querySelector('#section-meals .dashboard-table tr[data-meal-id="'+mealId+'"]');
            if (row) row.remove();
            // If table is now empty, show empty state
            let table = document.querySelector('#section-meals .dashboard-table');
            if (table && table.querySelectorAll('tr[data-meal-id]').length === 0) {
                let empty = document.createElement('div');
                empty.className = 'empty-state';
                empty.innerHTML = '<i class="fa-solid fa-bowl-food"></i><div>No meals found.<br>Click <b>+ Add New Meal</b> to publish your first meal.</div>';
                table.parentNode.appendChild(empty);
            }
        }
    </script>
</body>
</html>
