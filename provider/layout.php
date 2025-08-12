<?php
// Usage: include this file at the top of each admin page for consistent styling
$page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- FontAwesome for additional icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary: #4361ee;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --info: #4895ef;
        --warning: #f72585;
        --danger: #e63946;
        --light: #f8f9fa;
        --dark: #212529;
        --sidebar-bg: #1a1a2e;
        --sidebar-hover: #16213e;
        --card-shadow: 0 10px 20px rgba(0,0,0,0.1);
        --transition: all 0.3s ease;
    }
    
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
    }
    
    .admin-layout {
        display: flex;
        min-height: 100vh;
    }
    
    .sidebar {
        width: 250px;
        background: var(--sidebar-bg);
        color: #fff;
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0; left: 0; bottom: 0;
        z-index: 100;
        box-shadow: 0 0 25px rgba(0,0,0,0.2);
        transition: var(--transition);
        transform: translateX(0);
    }
    
    .sidebar .logo {
        padding: 25px 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        transition: var(--transition);
    }
    
    .sidebar .logo h2 {
        margin: 0;
        font-size: 1.8rem;
        color: #fff;
        font-weight: 700;
        letter-spacing: 1px;
    }
    
    .sidebar .logo i {
        color: var(--success);
        margin-right: 10px;
        animation: pulse 2s infinite;
    }
    
    .sidebar nav {
        flex: 1;
        padding: 25px 0;
    }
    
    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar li {
        margin-bottom: 8px;
    }
    
    .sidebar a {
        display: flex;
        align-items: center;
        padding: 15px 25px;
        color: #bdc3c7;
        text-decoration: none;
        font-size: 1.05rem;
        transition: var(--transition);
        border-left: 4px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .sidebar a:before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.05);
        transform: translateX(-100%);
        transition: var(--transition);
        z-index: -1;
    }
    
    .sidebar a:hover:before {
        transform: translateX(0);
    }
    
    .sidebar a:hover, .sidebar a.active {
        background: var(--sidebar-hover);
        color: #fff;
        border-left: 4px solid var(--success);
        transform: translateX(5px);
    }
    
    .sidebar a.active {
        background: linear-gradient(90deg, var(--sidebar-hover), rgba(22, 33, 62, 0.8));
    }
    
    .sidebar .icon {
        margin-right: 15px;
        font-size: 1.2em;
        width: 26px;
        text-align: center;
        transition: var(--transition);
    }
    
    .sidebar a:hover .icon {
        transform: scale(1.2);
    }
    
    .main-content {
        margin-left: 250px;
        flex: 1;
        padding: 30px;
        background: #f0f2f5;
        min-height: 100vh;
        transition: var(--transition);
    }
    
    .page-header {
        background: #fff;
        padding: 25px;
        margin-bottom: 30px;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        animation: fadeInDown 0.5s ease;
    }
    
    .page-header h1 {
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 5px;
        animation: slideInLeft 0.5s ease;
    }
    
    .page-header .text-muted {
        font-size: 1.1rem;
        animation: slideInLeft 0.7s ease;
    }
    
    .card {
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: none;
        margin-bottom: 25px;
        transition: var(--transition);
        animation: fadeIn 0.5s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 600;
        padding: 20px 25px;
        border-radius: 12px 12px 0 0 !important;
    }
    
    .stat-card {
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: none;
        margin-bottom: 25px;
        transition: var(--transition);
        overflow: hidden;
        position: relative;
        animation: fadeInUp 0.5s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    
    .stat-card:before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--success));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.5s ease;
    }
    
    .stat-card:hover:before {
        transform: scaleX(1);
    }
    
    .stat-card .card-body {
        padding: 25px;
    }
    
    .stat-card .stat-icon {
        font-size: 2.8rem;
        transition: var(--transition);
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 10px 0;
        transition: var(--transition);
    }
    
    .stat-card:hover .stat-number {
        color: var(--primary);
    }
    
    .table {
        box-shadow: none;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: var(--dark);
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .table-hover tbody tr {
        transition: var(--transition);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
        transform: scale(1.01);
    }
    
    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: var(--transition);
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
    }
    
    .btn:active {
        transform: translateY(-1px);
    }
    
    .btn-primary {
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        border: none;
    }
    
    .btn-success {
        background: linear-gradient(90deg, var(--success), #3a0ca3);
        border: none;
    }
    
    .btn-info {
        background: linear-gradient(90deg, var(--info), #3a0ca3);
        border: none;
    }
    
    .btn-warning {
        background: linear-gradient(90deg, var(--warning), #b5179e);
        border: none;
    }
    
    .btn-danger {
        background: linear-gradient(90deg, var(--danger), #d00000);
        border: none;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #e1e5eb;
        transition: var(--transition);
        box-shadow: none;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        transform: scale(1.02);
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--dark);
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: var(--transition);
    }
    
    .status-badge:hover {
        transform: scale(1.05);
    }
    
    .badge-active, .badge-confirmed {
        background: linear-gradient(90deg, #4cc9f0, #3a0ca3);
        color: white;
    }
    
    .badge-pending, .badge-open {
        background: linear-gradient(90deg, var(--warning), #b5179e);
        color: white;
    }
    
    .badge-cancelled, .badge-resolved {
        background: linear-gradient(90deg, var(--danger), #d00000);
        color: white;
    }
    
    .badge-completed {
        background: linear-gradient(90deg, #2ec4b6, #1a936f);
        color: white;
    }
    
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
    
    .bounce {
        animation: bounce 1s infinite;
    }
    
    @media (max-width: 992px) {
        .sidebar { width: 70px; }
        .sidebar .logo h2, .sidebar a span { display: none; }
        .sidebar .logo { padding: 20px 0; }
        .sidebar .logo h2 { font-size: 1.2rem; }
        .sidebar a { justify-content: center; padding: 15px 0; }
        .sidebar .icon { margin-right: 0; }
        .main-content { margin-left: 70px; }
    }
    
    @media (max-width: 768px) {
        .sidebar { 
            width: 0; 
            transform: translateX(-100%);
        }
        .sidebar.active {
            transform: translateX(0);
            width: 250px;
        }
        .main-content { margin-left: 0; }
        .main-content.shifted {
            transform: translateX(250px);
        }
    }
</style>