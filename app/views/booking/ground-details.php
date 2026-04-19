<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Ground Details - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];

// Get ground ID from URL parameter
$groundId = $_GET['id'] ?? 1;
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* CSS Custom Properties */
        :root {
            --primary-color: #2563eb;
            --primary-dark: #64748b;
            --primary-light: #f1f5f9;
            --secondary-color: #0891b2;
            --accent-color: #ffc107;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --text-light: #adb5bd;
            --text-white: #ffffff;
            --background-white: #ffffff;
            --background-light: #f8f9fa;
            --background-dark: #343a40;
            --border-color: #e9ecef;
            --shadow-light: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-medium: 0 4px 16px rgba(0,0,0,0.12);
            --shadow-heavy: 0 8px 32px rgba(0,0,0,0.16);
            --border-radius: 8px;
            --border-radius-lg: 12px;
            --border-radius-xl: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background-color: var(--background-white);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Loading State */
        .loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            flex-direction: column;
            gap: 1rem;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Breadcrumb */
        .breadcrumb {
            padding: 1rem 0;
            background: var(--background-light);
        }

        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            color: var(--primary-color);
        }

        .breadcrumb .current {
            color: var(--text-primary);
            font-weight: 600;
        }

        /* Ground Details Container */
        .ground-details {
            padding: 2rem 0;
        }

        /* Ground Header */
        .ground-header {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .ground-gallery {
            position: relative;
        }

        .main-image {
            width: 100%;
            height: 400px;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-medium);
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .main-image:hover img {
            transform: scale(1.05);
        }

        /* Ground Info */
        .ground-info {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .ground-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .ground-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .rating-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stars {
            display: flex;
            gap: 2px;
        }

        .star {
            color: var(--accent-color);
            font-size: 1.2rem;
        }

        .rating-text {
            font-weight: 600;
            color: var(--text-primary);
        }

        .reviews {
            color: var(--text-secondary);
        }

        .price-section {
            background: var(--primary-light);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }

        .price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .price-unit {
            font-size: 1rem;
            color: var(--text-secondary);
        }

        .book-button {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--text-white);
            border: none;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .book-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        /* Ground Content */
        .ground-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .content-section {
            background: var(--background-white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-light);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--background-light);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .feature-item:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: var(--text-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .feature-text {
            font-weight: 500;
            color: var(--text-primary);
        }

        /* Availability Section */
        .availability-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.5rem;
        }

        .day-slot {
            text-align: center;
            padding: 1rem 0.5rem;
            background: var(--background-light);
            border-radius: var(--border-radius);
            border: 2px solid transparent;
            transition: var(--transition);
            cursor: pointer;
        }

        .day-slot:hover {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .day-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .day-hours {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Sidebar */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .sidebar-section {
            background: var(--background-white);
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-light);
        }

        .coach-mini-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--border-radius);
            background: var(--background-light);
            transition: var(--transition);
        }
        .coach-mini-card:hover {
            background: #e0eaff;
        }

        .map-container {
            height: 300px;
            background: var(--background-light);
            border-radius: var(--border-radius);
            position: relative;
            overflow: hidden;
        }

        .map-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: var(--border-radius);
        }

        .map-loading-content {
            text-align: center;
            padding: 2rem;
        }

        .map-loading-spinner {
            margin-bottom: 1rem;
        }

        .spinner-ring {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        .map-loading-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .contact-icon {
            width: 35px;
            height: 35px;
            background: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .contact-text {
            color: var(--text-secondary);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .ground-header,
            .ground-content {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .ground-title {
                font-size: 2rem;
            }

            .main-image {
                height: 300px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .availability-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .ground-title {
                font-size: 1.5rem;
            }

            .price {
                font-size: 1.5rem;
            }

            .content-section,
            .sidebar-section {
                padding: 1.5rem;
            }
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .back-button:hover {
            color: var(--primary-color);
            gap: 0.75rem;
        }

        .error-container {
            text-align: center;
            padding: 3rem 0;
        }

        .error-title {
            font-size: 2rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .error-message {
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        /* PROFESSIONAL BOOKING MODAL STYLES */
        .booking-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        .booking-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .booking-modal-content {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-heavy);
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideIn 0.3s ease;
            position: relative;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .booking-modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            position: relative;
        }

        .modal-close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .booking-modal-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .booking-modal-subtitle {
            opacity: 0.9;
            font-size: 1rem;
        }

        .booking-modal-body {
            padding: 2rem;
        }

        .facility-info {
            background: var(--background-light);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .facility-icon-large {
            font-size: 3rem;
            color: var(--primary-color);
            background: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-light);
        }

        .facility-details h3 {
            font-size: 1.3rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .facility-location-modal {
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .facility-price-modal {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .booking-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-input, .form-select, .form-textarea {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            font-family: inherit;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .availability-status {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin: 1rem 0;
            display: none;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .availability-status.available {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .availability-status.unavailable {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .availability-status.checking {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        /*  Slot Grid  */
        .slot-grid-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .slot-grid-label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .slot-grid-loading {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .slot-grid-closed {
            padding: 14px;
            background: #f3f4f6;
            border-radius: 8px;
            color: #6b7280;
            font-size: 0.875rem;
            text-align: center;
        }
        .slot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 8px;
        }
        .slot-btn {
            padding: 10px 6px;
            border-radius: 8px;
            border: 2px solid transparent;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s;
            line-height: 1.3;
            position: relative;
        }
        .slot-btn.available {
            background: #f0fdf4;
            border-color: #86efac;
            color: #166534;
        }
        .slot-btn.available:hover {
            background: #dcfce7;
            border-color: #4ade80;
            transform: translateY(-1px);
        }
        .slot-btn.selected {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
            box-shadow: 0 4px 12px rgba(37,99,235,.3);
        }
        .slot-btn.in-range {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1d4ed8;
        }
        .slot-btn.booked {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #991b1b;
            cursor: not-allowed;
            opacity: 0.8;
        }
        .slot-btn.maintenance {
            background: #fff7ed;
            border-color: #fdba74;
            color: #9a3412;
            cursor: not-allowed;
            opacity: 0.8;
        }
        .slot-btn.blocked {
            background: #fffbeb;
            border-color: #fcd34d;
            color: #78350f;
            cursor: not-allowed;
            opacity: 0.8;
        }
        .slot-btn.closed {
            background: #f9fafb;
            border-color: #d1d5db;
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .slot-btn.unavailable-range {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #991b1b;
            opacity: 0.8;
        }
        .slot-time { display: block; font-size: 0.78rem; }
        .slot-status-label { display: block; font-size: 0.68rem; margin-top: 2px; opacity: 0.8; }
        .slot-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 0.75rem;
            color: #64748b;
        }
        .slot-legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .slot-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            border: 1.5px solid;
        }
        .legend-available  { background: #f0fdf4; border-color: #86efac; }
        .legend-selected   { background: #2563eb; border-color: #2563eb; }
        .legend-booked     { background: #fef2f2; border-color: #fca5a5; }
        .legend-closed     { background: #f9fafb; border-color: #d1d5db; }
        .slot-selected-info {
            padding: 10px 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #1d4ed8;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .booking-summary {
            background: var(--background-light);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .summary-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-color);
            padding-top: 1rem;
        }

        .booking-modal-footer {
            padding: 1.5rem 2rem;
            background: var(--background-light);
            border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn-modal-cancel {
            padding: 0.75rem 1.5rem;
            background: transparent;
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-modal-cancel:hover {
            background: var(--background-light);
            border-color: var(--text-secondary);
        }

        .btn-modal-confirm {
            padding: 0.75rem 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-modal-confirm:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-modal-confirm:disabled {
            background: var(--text-light);
            cursor: not-allowed;
            transform: none;
        }

        .booking-loading {
            display: none;
        }

        .booking-loading.show {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
    </style>
<!-- Ground Details Content -->

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="<?= $_base ?>/">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="<?= $_base ?>/book-ground">Book Ground</a>
                <i class="fas fa-chevron-right"></i>
                <span class="current" id="breadcrumb-current">Ground Details</span>
            </nav>
        </div>
    </div>

    <!-- Back Button -->
    <div class="container">
        <a href="<?= $_base ?>/book-ground" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Search
        </a>
    </div>

    <!-- Loading State -->
    <div id="loading" class="loading-container">
        <div class="loading-spinner"></div>
        <p>Loading ground details...</p>
    </div>

    <!-- Error State -->
    <div id="error-state" class="container" style="display: none;">
        <div class="error-container">
            <h2 class="error-title">Ground Not Found</h2>
            <p class="error-message">The requested ground could not be found.</p>
            <a href="<?= $_base ?>/book-ground" class="book-button" style="width: auto;">
                <i class="fas fa-search"></i>
                Browse All Grounds
            </a>
        </div>
    </div>

    <!-- Ground Details Content -->
    <div id="ground-content" class="container ground-details" style="display: none;">
        <!-- Ground Header -->
        <div class="ground-header">
            <div class="ground-gallery">
                <div class="main-image">
                    <img id="ground-image" src="" alt="Ground Image">
                </div>
            </div>
            
            <div class="ground-info">
                <h1 id="ground-title" class="ground-title"></h1>
                <div class="ground-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span id="ground-location"></span>
                </div>
                
                <div class="rating-section">
                    <div class="rating">
                        <div id="ground-stars" class="stars"></div>
                        <span id="ground-rating" class="rating-text"></span>
                    </div>
                    <span id="ground-reviews" class="reviews"></span>
                </div>
                
                <div class="price-section">
                    <div class="price">
                        LKR <span id="ground-price"></span>
                        <span class="price-unit">per hour</span>
                    </div>
                </div>
                
                <button class="book-button" onclick="bookGround()">
                    <i class="fas fa-calendar-check"></i>
                    Book Now
                </button>
            </div>
        </div>

        <!-- Ground Content -->
        <div class="ground-content">
            <div class="main-content">
                <!-- Description -->
                <div class="content-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Description
                    </h3>
                    <p id="ground-description"></p>
                </div>

                <!-- Features -->
                <div class="content-section">
                    <h3 class="section-title">
                        <i class="fas fa-star"></i>
                        Features & Amenities
                    </h3>
                    <div id="ground-features" class="features-grid"></div>
                </div>

                <!-- Availability -->
                <div class="content-section">
                    <h3 class="section-title">
                        <i class="fas fa-clock"></i>
                        Availability
                    </h3>
                    <div id="ground-availability" class="availability-grid"></div>
                </div>
            </div>

            <div class="sidebar">
                <!-- Map -->
                <div class="sidebar-section">
                    <h3 class="section-title">
                        <i class="fas fa-map"></i>
                        Location
                    </h3>
                    <div class="map-container">
                        <div id="ground-map" style="width: 100%; height: 100%; border-radius: 8px;"></div>
                        <div id="map-loading-overlay" class="map-loading-overlay">
                            <div class="map-loading-content">
                                <div class="map-loading-spinner">
                                    <div class="spinner-ring"></div>
                                </div>
                                <p class="map-loading-text">Loading map...</p>
                            </div>
                        </div>
                    </div>
                    <div class="location-info">
                        <div class="ground-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="sidebar-location">Loading location...</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="sidebar-section">
                    <h3 class="section-title">
                        <i class="fas fa-phone"></i>
                        Contact
                    </h3>
                    <div id="ground-contact" class="contact-info">
                        <!-- Contact info will be populated dynamically -->
                    </div>
                </div>

                <!-- Coaches at this facility -->
                <div class="sidebar-section" id="coaches-section" style="display:none;">
                    <h3 class="section-title">
                        <i class="fas fa-user-tie"></i>
                        Coaches Here
                    </h3>
                    <div id="facility-coaches-list"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Booking Modal -->
    <div id="booking-modal" class="booking-modal">
        <div class="booking-modal-content">
            <div class="booking-modal-header">
                <button class="modal-close-btn" onclick="closeBookingModal()">
                    <i class="fas fa-times"></i>
                </button>
                <h2 class="booking-modal-title">Book Sports Facility</h2>
                <p class="booking-modal-subtitle">Complete your reservation details</p>
            </div>

            <div class="booking-modal-body">
                <!-- Facility Information -->
                <div class="facility-info">
                    <div class="facility-icon-large">
                        <i id="modal-facility-icon" class="fas fa-sports"></i>
                    </div>
                    <div class="facility-details">
                        <h3 id="modal-facility-name">Facility Name</h3>
                        <div class="facility-location-modal">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="modal-facility-location">Location</span>
                        </div>
                        <div class="facility-price-modal">
                            LKR <span id="modal-facility-price">0</span> per hour
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <form id="booking-form" class="booking-form">
                    <!-- Hidden time inputs used by confirmBooking / checkAvailability -->
                    <input type="hidden" id="start-time" value="">
                    <input type="hidden" id="end-time"   value="">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar"></i>
                                Booking Date *
                            </label>
                            <input type="date" id="booking-date" class="form-input" required min=""
                                   onchange="loadSlotGrid(); updateBookingSummary();">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-clock"></i>
                                Duration *
                            </label>
                            <select id="booking-duration" class="form-select" required onchange="onDurationChange()">
                                <option value="">Select Duration</option>
                                <option value="1">1 Hour</option>
                                <option value="2">2 Hours</option>
                                <option value="3">3 Hours</option>
                                <option value="4">4 Hours</option>
                                <option value="5">5 Hours</option>
                                <option value="6">6 Hours</option>
                            </select>
                        </div>
                    </div>

                    <!-- Slot grid — shown after a date is picked -->
                    <div class="slot-grid-section" id="slot-grid-section" style="display:none;">
                        <label class="slot-grid-label">
                            <i class="fas fa-th"></i>
                            Select Start Time *
                        </label>
                        <div id="slot-grid-inner">
                            <div class="slot-grid-loading">
                                <i class="fas fa-spinner fa-spin"></i> Loading available slots…
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="slot-legend">
                            <span class="slot-legend-item">
                                <span class="slot-legend-dot legend-available"></span> Available
                            </span>
                            <span class="slot-legend-item">
                                <span class="slot-legend-dot legend-selected"></span> Selected
                            </span>
                            <span class="slot-legend-item">
                                <span class="slot-legend-dot legend-booked"></span> Booked
                            </span>
                            <span class="slot-legend-item">
                                <span class="slot-legend-dot legend-closed"></span> Unavailable
                            </span>
                        </div>
                        <!-- Selected time display -->
                        <div class="slot-selected-info" id="slot-selected-info" style="display:none;">
                            <i class="fas fa-check-circle"></i>
                            <span id="slot-selected-text"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-comment"></i>
                            Special Requests (Optional)
                        </label>
                        <textarea id="special-requests" class="form-textarea" placeholder="Any special requirements or notes..."></textarea>
                    </div>

                    <!-- Availability Status -->
                    <div id="availability-status" class="availability-status">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Checking availability...</span>
                    </div>

                    <!-- Booking Summary -->
                    <div class="booking-summary">
                        <h4 class="summary-title">Booking Summary</h4>
                        <div class="summary-row">
                            <span>Date:</span>
                            <span id="summary-date">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Time:</span>
                            <span id="summary-time">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Duration:</span>
                            <span id="summary-duration">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Rate:</span>
                            <span id="summary-rate">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Total Amount:</span>
                            <span id="summary-total">FREE</span>
                        </div>
                    </div>
                </form>
            </div>

            <div class="booking-modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeBookingModal()">
                    Cancel
                </button>
                <button type="button" class="btn-modal-confirm" id="confirm-booking-btn" onclick="confirmBooking()">
                    <i id="booking-loading" class="fas fa-spinner booking-loading"></i>
                    <span id="confirm-booking-text">Confirm Booking</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Ground data will be loaded here
        let currentGround = null;

        // Feature icons mapping
        const featureIcons = {
            'Floodlights': 'fas fa-lightbulb',
            'Parking': 'fas fa-parking',
            'Changing Rooms': 'fas fa-door-open',
            'Equipment Rental': 'fas fa-tools',
            'Clay Court': 'fas fa-circle',
            'Coaching Available': 'fas fa-user-graduate',
            'Air Conditioning': 'fas fa-snowflake',
            'Indoor': 'fas fa-home',
            'Spectator Seating': 'fas fa-chair',
            'Sound System': 'fas fa-volume-up',
            'Professional Pitch': 'fas fa-futbol',
            'Pavilion': 'fas fa-building',
            'Equipment Storage': 'fas fa-archive',
            'Scoreboard': 'fas fa-chart-line',
            'Multiple Courts': 'fas fa-th-large',
            'Olympic Pool': 'fas fa-swimming-pool',
            'Diving Board': 'fas fa-swimming-pool',
            'Locker Rooms': 'fas fa-lock',
            'Poolside Cafe': 'fas fa-coffee'
        };

        // Load ground data from database API
        async function loadGroundData() {
            try {
                // Get ground ID from URL parameter
                const urlParams = new URLSearchParams(window.location.search);
                const groundIdParam = urlParams.get('id');
                const groundId = groundIdParam ? parseInt(groundIdParam) : 1;
                
                if (!groundId || groundId <= 0) {
                    console.log('Invalid ground ID:', groundId);
                    showError();
                    return;
                }

                console.log('Loading ground with ID:', groundId);
                
                // Fetch ground details from database API
                const response = await fetch(`${window.BASE_URL||""}/api/ground/${groundId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('API Response:', result);
                
                if (result.success && result.data) {
                    currentGround = result.data;
                    console.log('Found ground:', currentGround.name);
                    displayGroundDetails(currentGround);
                    loadFacilityCoaches(groundId);
                } else {
                    console.log('Ground not found:', result.message);
                    showError();
                }
            } catch (error) {
                console.error('Error loading ground data:', error);
                showError();
            }
        }

        // Display ground details
        function displayGroundDetails(ground) {
            // Hide loading, show content
            document.getElementById('loading').style.display = 'none';
            document.getElementById('ground-content').style.display = 'block';

            // Update page title and breadcrumb
            document.title = `${ground.name} - GoPlay Sports Platform`;
            document.getElementById('breadcrumb-current').textContent = ground.name;

            // Basic info - handle database structure
            document.getElementById('ground-title').textContent = ground.name || 'Ground';
            
            // Location - combine address and city
            const location = ground.address ? `${ground.address}, ${ground.city}` : ground.city || ground.location || 'Location TBD';
            document.getElementById('ground-location').textContent = location;
            
            // Also update sidebar location
            const sidebarLocation = document.getElementById('sidebar-location');
            if (sidebarLocation) {
                sidebarLocation.textContent = location;
            }
            
            // Image - use sport-specific image based on category (always use sport-specific images)
            const categoryName = ground.category_name ? ground.category_name.toLowerCase() : 'general';
            const groundImages = {
                'football': '/public/assets/images/grounds/football-ground.jpg',
                'cricket': '/public/assets/images/grounds/cricket-ground.jpg',
                'tennis': '/public/assets/images/grounds/tennis-court.jpg',
                'basketball': '/public/assets/images/grounds/basketball-court.jpg',
                'badminton': '/public/assets/images/grounds/badminton-court.jpg',
                'volleyball': '/public/assets/images/grounds/football-ground.jpg',
                'swimming': '/public/assets/images/grounds/football-ground.jpg'
            };
            let imageUrl = groundImages[categoryName] || '/public/assets/images/grounds/football-ground.jpg';
            document.getElementById('ground-image').src = imageUrl;
            document.getElementById('ground-image').alt = ground.name || 'Ground Image';
            
            // Description
            document.getElementById('ground-description').textContent = ground.description || 'A great facility for sports activities.';
            
            // Price - use hourly_rate from database
            const price = ground.hourly_rate || ground.price || 0;
            document.getElementById('ground-price').textContent = price.toLocaleString();

            // Rating - use database rating and review count
            const rating = ground.rating || 4.5;
            const reviewCount = ground.reviews || ground.total_reviews || 0;
            displayRating(rating, reviewCount);

            // Features - use amenities from database
            const features = ground.amenities || ground.features || ['Parking', 'Changing Rooms'];
            displayFeatures(features);

            // Availability - use availability from API response
            displayAvailability(ground.availability || getDefaultAvailability());

            // Contact information
            displayContactInfo(ground);

            // Update map location
            updateGroundLocation(ground);
        }

        // Default availability if not provided
        function getDefaultAvailability() {
            return {
                'monday': ['6:00 AM - 10:00 PM'],
                'tuesday': ['6:00 AM - 10:00 PM'],
                'wednesday': ['6:00 AM - 10:00 PM'],
                'thursday': ['6:00 AM - 10:00 PM'],
                'friday': ['6:00 AM - 10:00 PM'],
                'saturday': ['7:00 AM - 9:00 PM'],
                'sunday': ['8:00 AM - 8:00 PM']
            };
        }

        // Display rating stars
        function displayRating(rating, reviewCount) {
            const starsContainer = document.getElementById('ground-stars');
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 !== 0;

            let starsHTML = '';
            
            // Full stars
            for (let i = 0; i < fullStars; i++) {
                starsHTML += '<i class="fas fa-star star"></i>';
            }
            
            // Half star
            if (hasHalfStar) {
                starsHTML += '<i class="fas fa-star-half-alt star"></i>';
            }
            
            // Empty stars
            const remainingStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
            for (let i = 0; i < remainingStars; i++) {
                starsHTML += '<i class="far fa-star star"></i>';
            }

            starsContainer.innerHTML = starsHTML;
            document.getElementById('ground-rating').textContent = rating.toFixed(1);
            document.getElementById('ground-reviews').textContent = `(${reviewCount} reviews)`;
        }

        // Display features
        function displayFeatures(features) {
            const featuresContainer = document.getElementById('ground-features');
            
            const featuresHTML = features.map(feature => {
                const icon = featureIcons[feature] || 'fas fa-check';
                return `
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="${icon}"></i>
                        </div>
                        <span class="feature-text">${feature}</span>
                    </div>
                `;
            }).join('');

            featuresContainer.innerHTML = featuresHTML;
        }

        // Display availability
        function displayAvailability(availability) {
            const availabilityContainer = document.getElementById('ground-availability');
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

            const availabilityHTML = days.map((day, index) => {
                const hours = availability[day] ? availability[day][0] : 'Closed';
                return `
                    <div class="day-slot">
                        <div class="day-name">${dayNames[index]}</div>
                        <div class="day-hours">${hours}</div>
                    </div>
                `;
            }).join('');

            availabilityContainer.innerHTML = availabilityHTML;
        }

        // Display contact information
        function displayContactInfo(ground) {
            const contactContainer = document.getElementById('ground-contact');
            
            let contactHTML = '';
            
            // Phone
            const phone = ground.phone || ground.contact_phone || '+94 11 234 5678';
            contactHTML += `
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <span class="contact-text">${phone}</span>
                </div>
            `;
            
            // Email
            const email = ground.email || ground.contact_email || 'info@goplay.lk';
            contactHTML += `
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <span class="contact-text">${email}</span>
                </div>
            `;
            
            // Owner name (if available)
            if (ground.first_name && ground.last_name) {
                contactHTML += `
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="contact-text">${ground.first_name} ${ground.last_name}</span>
                    </div>
                `;
            }
            
            // Operating hours
            contactHTML += `
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="contact-text">Daily: 6AM - 10PM</span>
                </div>
            `;

            contactContainer.innerHTML = contactHTML;
        }

        // Show error state
        function showError() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('error-state').style.display = 'block';
        }

        // Load coaches linked to this facility
        async function loadFacilityCoaches(facilityId) {
            try {
                const r = await fetch(`${window.BASE_URL||""}/api/facility/${facilityId}/coaches`);
                const d = await r.json();
                if (!d.success || !d.coaches || !d.coaches.length) return;

                const section = document.getElementById('coaches-section');
                const list    = document.getElementById('facility-coaches-list');
                section.style.display = '';

                list.innerHTML = d.coaches.map(c => `
                    <a href="<?= $_base ?>/coach-profile/${c.coach_id}" style="display:block;text-decoration:none;color:inherit;margin-bottom:12px;">
                        <div class="coach-mini-card">
                            <img src="${c.profile_picture || '/public/assets/images/default-avatar.png'}"
                                 alt="${c.first_name} ${c.last_name}"
                                 style="width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:600;font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    ${c.first_name} ${c.last_name}
                                    ${c.is_primary ? '<span style="background:#dbeafe;color:#1d4ed8;font-size:.7rem;padding:1px 7px;border-radius:999px;margin-left:4px;font-weight:600;">Primary</span>' : ''}
                                </div>
                                <div style="font-size:.8rem;color:var(--text-secondary);">${c.sport_name || ''}</div>
                                ${c.hourly_rate ? `<div style="font-size:.8rem;font-weight:600;color:var(--primary-color);">Rs. ${Number(c.hourly_rate).toLocaleString()}/hr</div>` : ''}
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-light);font-size:.75rem;"></i>
                        </div>
                    </a>`).join('');
            } catch (e) {
                console.warn('Could not load facility coaches:', e);
            }
        }

        // Book ground function - opens the booking modal
        function bookGround() {
            if (!currentGround) {
                alert('Ground information not loaded. Please try again.');
                return;
            }

            // Populate modal with facility information
            document.getElementById('modal-facility-name').textContent = currentGround.name;
            const location = currentGround.address ? `${currentGround.address}, ${currentGround.city}` : currentGround.city;
            document.getElementById('modal-facility-location').textContent = location;
            document.getElementById('modal-facility-price').textContent = new Intl.NumberFormat().format(currentGround.hourly_rate || 0);

            // Set sport icon based on category
            const sportIcons = {
                'Football': 'fas fa-football-ball',
                'Cricket': 'fas fa-baseball-ball',
                'Tennis': 'fas fa-table-tennis',
                'Basketball': 'fas fa-basketball-ball',
                'Swimming': 'fas fa-swimmer',
                'Badminton': 'fas fa-shuttlecock',
                'Volleyball': 'fas fa-volleyball-ball'
            };
            const iconClass = sportIcons[currentGround.category_name] || 'fas fa-sports';
            document.getElementById('modal-facility-icon').className = iconClass;

            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('booking-date').min = today;

            // Reset form
            document.getElementById('booking-form').reset();
            document.getElementById('start-time').value = '';
            document.getElementById('end-time').value   = '';
            document.getElementById('availability-status').style.display = 'none';
            document.getElementById('slot-grid-section').style.display = 'none';
            document.getElementById('slot-selected-info').style.display = 'none';
            document.getElementById('confirm-booking-btn').disabled = true;
            slotData = []; selectedSlotIndex = -1;
            resetBookingSummary();

            // Show modal
            document.getElementById('booking-modal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // Global variables for booking
        let availabilityCheckTimeout = null;
        let slotData = [];          // slots fetched for the current date
        let selectedSlotIndex = -1; // index into slotData of the chosen start slot

        // Close booking modal
        function closeBookingModal() {
            document.getElementById('booking-modal').classList.remove('show');
            document.body.style.overflow = 'auto';
            if (availabilityCheckTimeout) clearTimeout(availabilityCheckTimeout);
        }

        /* 
           SLOT GRID
         */

        function fmt12(hhmm) {
            const [h, m] = hhmm.split(':').map(Number);
            const ampm = h < 12 ? 'AM' : 'PM';
            const h12  = h % 12 || 12;
            return m === 0 ? `${h12} ${ampm}` : `${h12}:${String(m).padStart(2,'0')} ${ampm}`;
        }

        async function loadSlotGrid() {
            const date       = document.getElementById('booking-date').value;
            const facilityId = currentGround?.id;
            const section    = document.getElementById('slot-grid-section');
            const inner      = document.getElementById('slot-grid-inner');

            // Reset selection
            selectedSlotIndex = -1;
            slotData = [];
            document.getElementById('start-time').value = '';
            document.getElementById('end-time').value   = '';
            document.getElementById('slot-selected-info').style.display = 'none';
            document.getElementById('availability-status').style.display = 'none';
            document.getElementById('confirm-booking-btn').disabled = true;

            if (!date || !facilityId) { section.style.display = 'none'; return; }

            section.style.display = 'flex';
            inner.innerHTML = '<div class="slot-grid-loading"><i class="fas fa-spinner fa-spin"></i> Loading slots…</div>';

            try {
                const r = await fetch(`${window.BASE_URL||""}/api/facility/${facilityId}/slots?date=${date}`);
                const d = await r.json();
                if (!d.success) throw new Error(d.error || 'Failed');
                slotData = d.slots || [];
                renderSlotGrid();
            } catch (e) {
                inner.innerHTML = `<div class="slot-grid-loading" style="color:#ef4444;"><i class="fas fa-exclamation-circle"></i> Could not load slots: ${e.message}</div>`;
            }
        }

        function renderSlotGrid() {
            const inner    = document.getElementById('slot-grid-inner');
            const duration = parseInt(document.getElementById('booking-duration').value) || 1;

            if (!slotData.length) {
                inner.innerHTML = '<div class="slot-grid-closed"><i class="fas fa-door-closed"></i> No time slots available for this day.</div>';
                return;
            }

            // Single all-day closed marker
            if (slotData[0]?.all_day) {
                inner.innerHTML = `<div class="slot-grid-closed"><i class="fas fa-ban"></i> ${slotData[0].reason}</div>`;
                return;
            }

            const html = slotData.map((slot, i) => {
                let cls = slot.status;
                let label = '';
                let title = slot.reason || '';
                let clickable = false;

                if (slot.status === 'available') {
                    // Check if there are enough consecutive available slots for the chosen duration
                    const canFit = canFitDuration(i, duration);
                    if (canFit) {
                        clickable = true;
                        if (i === selectedSlotIndex) cls = 'selected';
                        else if (selectedSlotIndex >= 0 && i > selectedSlotIndex && i < selectedSlotIndex + duration) cls = 'in-range';
                    } else if (i === selectedSlotIndex) {
                        cls = 'unavailable-range';
                        label = 'blocked range';
                        title = 'Not enough consecutive free slots for selected duration';
                    }
                } else {
                    const labels = { booked:'Booked', maintenance:'Maintenance', blocked:'Closed', closed:'Unavailable' };
                    label = labels[slot.status] || '';
                }

                return `<button type="button"
                    class="slot-btn ${cls}"
                    data-index="${i}"
                    title="${title}"
                    ${!clickable ? 'disabled' : `onclick="selectSlot(${i})"`}>
                    <span class="slot-time">${fmt12(slot.start)}</span>
                    ${label ? `<span class="slot-status-label">${label}</span>` : ''}
                </button>`;
            }).join('');

            inner.innerHTML = `<div class="slot-grid">${html}</div>`;
        }

        function canFitDuration(startIdx, duration) {
            if (startIdx + duration > slotData.length) return false;
            for (let i = startIdx; i < startIdx + duration; i++) {
                if (slotData[i].status !== 'available') return false;
            }
            return true;
        }

        function selectSlot(index) {
            const duration = parseInt(document.getElementById('booking-duration').value) || 1;

            if (!canFitDuration(index, duration)) return;

            selectedSlotIndex = index;

            const startSlot = slotData[index];
            const endSlot   = slotData[index + duration - 1];

            document.getElementById('start-time').value = startSlot.start_full;
            document.getElementById('end-time').value   = endSlot.end_full;

            // Update selected info banner
            const infoEl  = document.getElementById('slot-selected-info');
            const textEl  = document.getElementById('slot-selected-text');
            textEl.textContent = `${fmt12(startSlot.start)} – ${fmt12(endSlot.end)}  ·  ${duration} hour${duration > 1 ? 's' : ''}`;
            infoEl.style.display = 'flex';

            renderSlotGrid();       // re-render to update highlight
            updateBookingSummary();
            checkAvailability();
        }

        function onDurationChange() {
            // If a slot is already selected, re-validate the range with new duration
            if (selectedSlotIndex >= 0) {
                const duration = parseInt(document.getElementById('booking-duration').value) || 1;
                if (canFitDuration(selectedSlotIndex, duration)) {
                    selectSlot(selectedSlotIndex); // re-select with new duration
                } else {
                    // Range now blocked — clear selection
                    selectedSlotIndex = -1;
                    document.getElementById('start-time').value = '';
                    document.getElementById('end-time').value   = '';
                    document.getElementById('slot-selected-info').style.display = 'none';
                    document.getElementById('confirm-booking-btn').disabled = true;
                    renderSlotGrid();
                    updateBookingSummary();
                }
            } else {
                renderSlotGrid(); // re-render with new duration to update canFit highlights
                updateBookingSummary();
            }
        }

        // calculateEndTime kept for any legacy callers but now a no-op
        // (end-time is set directly by selectSlot)
        function calculateEndTime() {}

        // Update booking summary
        function updateBookingSummary() {
            console.log('=== updateBookingSummary called ===');
            const date = document.getElementById('booking-date').value;
            const startTime = document.getElementById('start-time').value;
            const endTime = document.getElementById('end-time').value;
            const duration = document.getElementById('booking-duration').value;

            console.log('Form values:', { date, startTime, endTime, duration });
            console.log('currentGround:', currentGround);

            if (date) {
                const dateObj = new Date(date);
                const formattedDate = dateObj.toLocaleDateString();
                document.getElementById('summary-date').textContent = formattedDate;
                console.log('Updated date:', formattedDate);
            }

            if (startTime && endTime) {
                const start = new Date(`1970-01-01T${startTime}`).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const end = new Date(`1970-01-01T${endTime}`).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const timeRange = `${start} - ${end}`;
                document.getElementById('summary-time').textContent = timeRange;
                console.log('Updated time:', timeRange);
            }

            if (duration) {
                const durationText = `${duration} hour${duration > 1 ? 's' : ''}`;
                document.getElementById('summary-duration').textContent = durationText;
                console.log('Updated duration:', durationText);
            }

            if (currentGround) {
                const hourlyRate = currentGround.hourly_rate || 0;
                const rateText = `LKR ${new Intl.NumberFormat().format(hourlyRate)} / hour`;
                document.getElementById('summary-rate').textContent = rateText;
                console.log('Updated rate:', rateText);

                // Calculate total amount based on duration
                if (duration && hourlyRate) {
                    const totalAmount = hourlyRate * parseInt(duration);
                    const totalText = `LKR ${new Intl.NumberFormat().format(totalAmount)}`;
                    document.getElementById('summary-total').textContent = totalText;
                    console.log('Updated total:', totalText, '(rate:', hourlyRate, 'x duration:', duration, ')');
                } else {
                    document.getElementById('summary-total').textContent = 'LKR 0';
                    console.log('No total calculated - missing duration or rate');
                }
            } else {
                console.log('No currentGround data available');
            }
        }

        // Reset booking summary
        function resetBookingSummary() {
            document.getElementById('summary-date').textContent = '-';
            document.getElementById('summary-time').textContent = '-';
            document.getElementById('summary-duration').textContent = '-';
            document.getElementById('summary-rate').textContent = '-';
            document.getElementById('summary-total').textContent = 'LKR 0';
        }

        // Check availability
        async function checkAvailability() {
            const facilityId = currentGround?.id;
            const date = document.getElementById('booking-date').value;
            const startTime = document.getElementById('start-time').value;
            const endTime = document.getElementById('end-time').value;

            if (!facilityId || !date || !startTime || !endTime) {
                return;
            }

            const statusElement = document.getElementById('availability-status');
            const confirmBtn = document.getElementById('confirm-booking-btn');

            // Show checking status
            statusElement.className = 'availability-status checking';
            statusElement.style.display = 'flex';
            statusElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Checking availability...</span>';
            confirmBtn.disabled = true;

            try {
                const response = await fetch(`${window.BASE_URL||""}/api/booking/availability?facility_id=${facilityId}&date=${date}&start_time=${startTime}&end_time=${endTime}`);
                const data = await response.json();

                if (data.success) {
                    if (data.available) {
                        statusElement.className = 'availability-status available';
                        statusElement.innerHTML = '<i class="fas fa-check-circle"></i> <span>Time slot is available!</span>';
                        confirmBtn.disabled = false;
                    } else {
                        const reason = data.reason || 'This time slot is not available.';
                        statusElement.className = 'availability-status unavailable';
                        statusElement.innerHTML = `<i class="fas fa-times-circle"></i> <span>${reason} Please select a different slot.</span>`;
                        confirmBtn.disabled = true;
                    }
                } else {
                    throw new Error(data.message || 'Failed to check availability');
                }
            } catch (error) {
                console.error('Availability check error:', error);
                statusElement.className = 'availability-status unavailable';
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span>Error checking availability. Please try again.</span>';
                confirmBtn.disabled = true;
            }
        }

        // Confirm booking
        async function confirmBooking() {
            if (!currentGround) {
                alert('Please select a facility first.');
                return;
            }

            const bookingData = {
                facility_id: currentGround.id,
                booking_date: document.getElementById('booking-date').value,
                start_time: document.getElementById('start-time').value,
                end_time: document.getElementById('end-time').value,
                special_requests: document.getElementById('special-requests').value || ''
            };

            // Validate required fields
            if (!bookingData.facility_id || !bookingData.booking_date || !bookingData.start_time || !bookingData.end_time) {
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state
            const loadingIcon = document.getElementById('booking-loading');
            const confirmText = document.getElementById('confirm-booking-text');
            const confirmBtn = document.getElementById('confirm-booking-btn');

            loadingIcon.classList.add('show');
            confirmText.textContent = 'Processing...';
            confirmBtn.disabled = true;

            try {
                const response = await fetch((window.BASE_URL||'')+'/api/booking/ground', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(bookingData)
                });

                const data = await response.json();

                if (data.success && data.requires_payment && data.payment_data) {
                    // Redirect to PayHere for payment
                    closeBookingModal();
                    launchPayHere(data.payment_data);
                } else if (data.success) {
                    alert('Booking submitted! ' + (data.message || ''));
                    closeBookingModal();
                    window.location.href = (window.BASE_URL || '') + '/my-bookings';
                } else {
                    throw new Error(data.message || 'Booking failed');
                }
            } catch (error) {
                console.error('Booking error:', error);
                alert('Booking failed: ' + error.message);
            } finally {
                // Reset loading state
                loadingIcon.classList.remove('show');
                confirmText.textContent = 'Confirm Booking';
                confirmBtn.disabled = false;
            }
        }

        // ── PayHere redirect helper ──────────────────────────────────────────
        function launchPayHere(pd) {
            const gatewayUrl = pd.sandbox
                ? 'https://sandbox.payhere.lk/pay/checkout'
                : 'https://www.payhere.lk/pay/checkout';

            const form = document.createElement('form');
            form.method  = 'POST';
            form.action  = gatewayUrl;
            form.style.display = 'none';

            const fields = ['merchant_id','return_url','cancel_url','notify_url',
                            'order_id','items','currency','amount',
                            'first_name','last_name','email','phone',
                            'address','city','country','hash'];
            fields.forEach(k => {
                if (pd[k] === undefined) return;
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = k;
                inp.value = pd[k];
                form.appendChild(inp);
            });

            document.body.appendChild(form);
            form.submit();
        }

        // Google Maps for Ground Details
        let groundMap;
        let groundMarker;

        function initGroundMap() {
            console.log('Initializing ground map...');
            
            // Show loading overlay
            showMapLoading(true);
            
            // Default location (will be updated when ground data loads)
            const defaultLocation = { lat: 6.9271, lng: 79.8612 };
            
            groundMap = new google.maps.Map(document.getElementById('ground-map'), {
                zoom: 15,
                center: defaultLocation,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                styles: [
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'off' }]
                    }
                ]
            });

            // Create marker (will be updated with sport-specific icon)
            groundMarker = new google.maps.Marker({
                position: defaultLocation,
                map: groundMap,
                title: 'Ground Location'
            });

            // Hide loading after map is ready
            google.maps.event.addListenerOnce(groundMap, 'idle', function() {
                showMapLoading(false);
            });

            console.log('Ground map initialized');
        }
        
        // Show/hide map loading overlay
        function showMapLoading(show) {
            const overlay = document.getElementById('map-loading-overlay');
            if (overlay) {
                overlay.style.display = show ? 'flex' : 'none';
            }
        }
        
        // Get sport-specific marker icon (same as book-ground page)
        function getSportMarkerIcon(sport) {
            const colors = {
                cricket: '#ff6b6b',
                tennis: '#4ecdc4', 
                football: '#45b7d1',
                basketball: '#f9ca24',
                badminton: '#6c5ce7',
                swimming: '#00cec9'
            };
            
            const sportKey = sport ? sport.toLowerCase() : 'general';
            
            return {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: colors[sportKey] || '#2563eb',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 3
            };
        }

        function updateGroundLocation(ground) {
            if (!groundMap || !groundMarker) return;

            // Use actual coordinates from database
            if (ground.latitude && ground.longitude) {
                const position = { 
                    lat: parseFloat(ground.latitude), 
                    lng: parseFloat(ground.longitude) 
                };

                // Update map center and marker with database coordinates
                groundMap.setCenter(position);
                groundMarker.setPosition(position);
                groundMarker.setTitle(ground.name);
                
                // Update marker icon based on sport category
                const sportIcon = getSportMarkerIcon(ground.category_name);
                groundMarker.setIcon(sportIcon);
                
                console.log(`Updated map for ${ground.name} at coordinates:`, position);
            } else {
                console.log(`No coordinates available for ${ground.name}, using default location`);
                // Fallback to default location if no coordinates in database
                const defaultPosition = { lat: 6.9271, lng: 79.8612 };
                groundMap.setCenter(defaultPosition);
                groundMarker.setPosition(defaultPosition);
                groundMarker.setTitle(ground.name || 'Sports Facility');
                
                // Set default icon
                const defaultIcon = getSportMarkerIcon('general');
                groundMarker.setIcon(defaultIcon);
            }

            // Add info window with actual ground data
            const locationText = ground.address ? `${ground.address}, ${ground.city}` : ground.city || 'Sports Facility';
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; max-width: 250px;">
                        <h4 style="margin: 0 0 8px 0; color: #2563eb;">${ground.name}</h4>
                        <p style="margin: 0 0 6px 0; color: #666; font-size: 14px;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>
                            ${locationText}
                        </p>
                        <p style="margin: 0 0 6px 0; color: #666; font-size: 14px;">
                            <i class="fas fa-tag" style="margin-right: 5px;"></i>
                            ${ground.category_name || 'Sports Facility'}
                        </p>
                        <p style="margin: 0; color: #2563eb; font-weight: 600; font-size: 14px;">
                            LKR ${ground.hourly_rate ? ground.hourly_rate.toLocaleString() : 'N/A'}/hour
                        </p>
                    </div>
                `
            });

            groundMarker.addListener('click', () => {
                infoWindow.open(groundMap, groundMarker);
            });
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadGroundData();
        });
    </script>

    <!-- Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3qKhJG9ulG0vgu9KxaG0NPXADLGxMr7k&callback=initGroundMap">
    </script>