<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Shop - GoPlay Sports Platform';
$additionalCSS = ['/public/css/components/cart.css'];
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$additionalJS = ['/public/js/cart-api.js'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Unified Design System */
        :root {
            --primary-color: #2563eb;
            --primary-dark: #64748b;
            --primary-light: #f1f5f9;
            --secondary-color: #0891b2;
            --accent-color: #0891b2;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --text-light: #adb5bd;
            --background-light: #f8f9fa;
            --background-white: #ffffff;
            --border-color: #e9ecef;
            --shadow-light: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-medium: 0 4px 16px rgba(0,0,0,0.12);
            --shadow-heavy: 0 8px 32px rgba(0,0,0,0.16);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background: var(--background-light);
        }

        .shop-container {
            min-height: 100vh;
        }

        /* Header Section */
        .page-header {
            background: #1e3a8a;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .header-text h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .header-text p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 500px;
        }

        .header-stats {
            display: flex;
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-primary, .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--background-white);
            color: var(--primary-color);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary-color);
        }

        /* Search Section */
        .search-section {
            background: var(--background-white);
            box-shadow: var(--shadow-light);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .search-bar {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 1rem;
            align-items: center;
        }

        .search-input-group {
            position: relative;
        }

        .search-input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        .filter-group {
            display: flex;
            gap: 1rem;
        }

        .filter-select {
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            background: white;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .search-btn {
            padding: 1rem 2rem;
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

        .search-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--background-white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            margin-bottom: 2rem;
        }

        .results-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .results-subtitle {
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        .sort-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            background: white;
            cursor: pointer;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: var(--background-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .product-image {
            height: 250px;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-color) 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-icon {
            font-size: 4rem;
            opacity: 0.8;
            color: white;
        }

        .product-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--danger-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .product-badge.new {
            background: var(--warning-color);
            color: var(--text-primary);
        }

        .product-badge.sale {
            background: var(--danger-color);
        }

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        .quick-view-btn {
            background: var(--background-white);
            color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .quick-view-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .product-content {
            padding: 2rem;
        }

        .product-category {
            color: var(--text-secondary);
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .product-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .product-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .stars {
            display: flex;
            gap: 2px;
        }

        .stars i {
            color: var(--warning-color);
            font-size: 0.9rem;
        }

        .rating-text {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .price-current {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .price-original {
            font-size: 1rem;
            color: var(--text-light);
            text-decoration: line-through;
        }

        .price-discount {
            background: var(--danger-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .product-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-outline {
            padding: 0.75rem 1rem;
            border: 2px solid var(--primary-color);
            background: transparent;
            color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.9rem;
            flex: 1;
            text-align: center;
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .product-actions .btn-primary {
            padding: 0.75rem 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
            flex: 1;
        }

        .product-actions .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Categories Section */
        .categories-section {
            background: var(--background-white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            margin-bottom: 2rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .category-item {
            text-align: center;
            padding: 1.5rem 1rem;
            background: var(--background-light);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: var(--text-primary);
        }

        .category-item:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .category-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Loading State */
        .loading-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .loading-spinner {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Cart Summary */
        .cart-summary {
            position: fixed;
            top: 50%;
            right: 2rem;
            transform: translateY(-50%);
            background: var(--background-white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-heavy);
            min-width: 200px;
            opacity: 0;
            pointer-events: none;
            transition: var(--transition);
        }

        .cart-summary.visible {
            opacity: 1;
            pointer-events: all;
        }

        .cart-icon {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-heavy);
            transition: var(--transition);
        }

        .cart-icon:hover {
            transform: scale(1.1);
            background: var(--primary-dark);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Professional Cart Styles - Inline for reliability */
        .cart-icon {
            position: fixed !important;
            bottom: 2rem !important;
            right: 2rem !important;
            width: 64px !important;
            height: 64px !important;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.5rem !important;
            cursor: pointer !important;
            box-shadow: 0 8px 32px rgba(37, 99, 235, 0.3) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            z-index: 9999 !important;
        }

        .cart-icon:hover {
            transform: translateY(-4px) scale(1.05) !important;
            box-shadow: 0 12px 40px rgba(37, 99, 235, 0.4) !important;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        }

        .cart-count {
            position: absolute !important;
            top: -6px !important;
            right: -6px !important;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: white !important;
            border-radius: 50% !important;
            min-width: 28px !important;
            height: 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            border: 3px solid white !important;
            animation: cartCountPulse 0.6s ease-out !important;
        }

        @keyframes cartCountPulse {
            0% { transform: scale(0.8); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .cart-summary {
            position: fixed !important;
            top: 2rem !important;
            right: 2rem !important;
            bottom: 2rem !important;
            width: 480px !important;
            transform: translateX(100%) !important;
            background: white !important;
            border-radius: 24px !important;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.2) !important;
            opacity: 0 !important;
            pointer-events: none !important;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1) !important;
            z-index: 9998 !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .cart-summary.visible {
            opacity: 1 !important;
            pointer-events: all !important;
            transform: translateX(0) !important;
        }

        .cart-summary-header {
            padding: 1.5rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }

        .cart-summary-title {
            font-size: 1.4rem !important;
            font-weight: 700 !important;
            color: #2563eb !important;
            margin: 0 !important;
        }

        .cart-close-btn {
            background: #dbeafe !important;
            border: none !important;
            border-radius: 50% !important;
            width: 36px !important;
            height: 36px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            color: #2563eb !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
        }

        .cart-close-btn:hover {
            background: #bfdbfe !important;
            color: #1d4ed8 !important;
            transform: scale(1.1) !important;
        }

        .cart-items-container {
            flex: 1 !important;
            overflow-y: auto !important;
            padding: 0 !important;
        }

        .cart-item {
            padding: 1.25rem 1.5rem !important;
            border-bottom: 1px solid #f8fafc !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
        }

        .cart-item:hover {
            background: #fafbfc !important;
        }

        .cart-item-image {
            width: 60px !important;
            height: 60px !important;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.5rem !important;
            color: #64748b !important;
            flex-shrink: 0 !important;
        }

        .cart-item-details {
            flex-grow: 1 !important;
            min-width: 0 !important;
        }

        .cart-item-name {
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            color: #1e293b !important;
            margin: 0 0 0.25rem 0 !important;
            line-height: 1.3 !important;
        }

        .cart-item-info {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            color: #64748b !important;
            font-size: 0.85rem !important;
            margin-bottom: 0.5rem !important;
        }

        .cart-item-quantity {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            background: #f8fafc !important;
            border-radius: 8px !important;
            padding: 0.25rem !important;
        }

        .qty-btn {
            background: white !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            width: 28px !important;
            height: 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
        }

        .qty-btn:hover {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }

        .qty-value {
            min-width: 24px !important;
            text-align: center !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
        }

        .cart-item-actions {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
            gap: 0.5rem !important;
        }

        .cart-item-price {
            font-weight: 700 !important;
            color: #2563eb !important;
            font-size: 1rem !important;
            text-align: right !important;
        }

        .remove-btn {
            background: #fef2f2 !important;
            color: #dc2626 !important;
            border: none !important;
            border-radius: 8px !important;
            width: 32px !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            font-size: 0.9rem !important;
        }

        .remove-btn:hover {
            background: #fee2e2 !important;
            transform: scale(1.05) !important;
        }

        .cart-empty {
            text-align: center !important;
            padding: 3rem 1.5rem !important;
            color: #64748b !important;
        }

        .cart-empty-icon {
            font-size: 3rem !important;
            color: #cbd5e1 !important;
            margin-bottom: 1rem !important;
        }

        .cart-empty-text {
            font-size: 1.1rem !important;
            margin-bottom: 0.5rem !important;
            color: #475569 !important;
        }

        .cart-empty-subtext {
            font-size: 0.9rem !important;
            color: #94a3b8 !important;
        }

        .cart-summary-footer {
            padding: 1.5rem !important;
            border-top: 1px solid #f1f5f9 !important;
            background: #fafbfc !important;
            border-radius: 0 0 20px 20px !important;
        }

        .cart-totals {
            margin-bottom: 1.5rem !important;
        }

        .cart-total-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 0.75rem !important;
        }

        .cart-total-row:last-child {
            margin-bottom: 0 !important;
            padding-top: 0.75rem !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        .cart-total-label {
            color: #64748b !important;
            font-size: 0.9rem !important;
        }

        .cart-total-value {
            font-weight: 600 !important;
            color: #1e293b !important;
        }

        .cart-total-row:last-child .cart-total-label,
        .cart-total-row:last-child .cart-total-value {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }

        .cart-actions {
            display: flex !important;
            gap: 0.75rem !important;
        }

        .cart-btn {
            flex: 1 !important;
            padding: 0.875rem 1.5rem !important;
            border: none !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.5rem !important;
        }

        .cart-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: white !important;
        }

        .cart-btn-primary:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3) !important;
        }

        .cart-btn-secondary {
            background: white !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
        }

        .cart-btn-secondary:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }

        .cart-notification {
            position: fixed !important;
            top: 2rem !important;
            right: 2rem !important;
            padding: 1rem 1.5rem !important;
            border-radius: 12px !important;
            color: white !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            z-index: 10000 !important;
            opacity: 0 !important;
            transform: translateX(100%) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            max-width: 350px !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .cart-notification.visible {
            opacity: 1 !important;
            transform: translateX(0) !important;
        }

        .cart-notification.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .cart-notification.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        }

        .cart-notification.info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        }

        .notification-icon {
            font-size: 1.2rem !important;
        }

        /* Responsive Design */
        @media (max-width: 1100px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 2rem;
            }

            .header-actions {
                width: 100%;
            }

            .search-bar {
                grid-template-columns: 1fr;
            }

            .filter-group {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .results-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 2rem;
            }

            .header-stats {
                justify-content: center;
            }

            .search-bar {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .filter-group {
                flex-direction: column;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .results-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 1rem;
            }

            .search-container {
                padding: 1rem;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .product-actions {
                flex-direction: column;
            }

            .header-text h1 {
                font-size: 2rem;
            }

            .header-text p {
                font-size: 1rem;
            }

            .header-stats {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }

            .filter-group {
                grid-template-columns: 1fr;
            }

            .header-actions .btn-primary,
            .header-actions .btn-secondary,
            .search-btn,
            .sort-select {
                width: 100%;
                justify-content: center;
            }

            .cart-icon {
                bottom: 1rem;
                right: 1rem;
            }
        }
    </style>
<div class="shop-container">

        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-text">
                    <h1>Sports Equipment Shop</h1>
                    <p>Premium sports gear and equipment for all your athletic needs</p>
                    <div class="header-stats">
                       
                    </div>
                </div>
                <div class="header-actions">
                    
                    
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <div class="search-container">
                <div class="search-bar">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-products" placeholder="Search for sports equipment..." class="search-input">
                    </div>
                    <div class="filter-group">
                        <select id="category-filter" class="filter-select">
                            <option value="">All Categories</option>
                            <option value="football">Football</option>
                            <option value="tennis">Tennis</option>
                            <option value="basketball">Basketball</option>
                            <option value="cricket">Cricket</option>
                            <option value="badminton">Badminton</option>
                            <option value="swimming">Swimming</option>
                            <option value="fitness">Fitness</option>
                        </select>
                        <select id="price-filter" class="filter-select">
                            <option value="">Any Price</option>
                            <option value="0-50">LKR 0 - 5,000</option>
                            <option value="50-100">LKR 5,000 - 10,000</option>
                            <option value="100+">LKR 10,000+</option>
                        </select>
                    </div>
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Categories Section -->
            <div class="categories-section">
                <h2>Shop by Category</h2>
                <div class="categories-grid" id="categories-grid">
                    <!-- Categories will be loaded dynamically -->
                </div>
            </div>

            <!-- Results Header -->
            <div class="results-header">
                <div class="results-info">
                    <h2>Featured Products</h2>
                    <p class="results-subtitle">Discover our top-rated sports equipment</p>
                </div>
                <div class="sort-controls">
                    <select class="sort-select">
                        <option value="featured">Featured</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="rating">Highest Rated</option>
                        <option value="newest">Newest</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-grid" id="products-grid">
                <!-- Product cards will be dynamically loaded here -->
                <div class="loading-state">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <p>Loading products...</p>
                </div>
            </div>
        </div>

        <!-- Cart Icon -->
        <button class="cart-icon" onclick="toggleCart()">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count" id="cart-count">0</span>
        </button>

        <!-- Professional Cart Summary -->
        <div class="cart-summary" id="cart-summary">
            <div class="cart-summary-header">
                <h3 class="cart-summary-title">Shopping Cart</h3>
                <button class="cart-close-btn" onclick="toggleCart()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="cart-items-container" id="cart-items-container">
                <div id="cart-items"></div>
                <div class="cart-empty" id="cart-empty" style="display: block;">
                    <div class="cart-empty-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="cart-empty-text">Your cart is empty</div>
                    <div class="cart-empty-subtext">Add some products to get started</div>
                </div>
            </div>
            
            <div class="cart-summary-footer" id="cart-footer" style="display: none;">
                <div class="cart-totals">
                    <div class="cart-total-row">
                        <span class="cart-total-label">Items (<span id="cart-item-count">0</span>)</span>
                        <span class="cart-total-value">LKR <span id="cart-subtotal">0</span></span>
                    </div>
                    <div class="cart-total-row">
                        <span class="cart-total-label">Shipping</span>
                        <span class="cart-total-value">FREE</span>
                    </div>
                    <div class="cart-total-row">
                        <span class="cart-total-label">Total</span>
                        <span class="cart-total-value">LKR <span id="cart-total">0</span></span>
                    </div>
                </div>
                
                <div class="cart-actions">
                    <button class="cart-btn cart-btn-secondary" onclick="clearCart()">
                        <i class="fas fa-trash"></i>
                        Clear Cart
                    </button>
                    <button class="cart-btn cart-btn-primary" onclick="checkout()">
                        <i class="fas fa-credit-card"></i>
                        Checkout
                    </button>
                </div>
            </div>
        </div>

</div>

    <script>
        // Products and categories data from PHP backend
        let products = <?php echo json_encode($products ?? []); ?>;
        let categories = <?php echo json_encode($categories ?? []); ?>;
        let featuredProducts = <?php echo json_encode($featuredProducts ?? []); ?>;
        let currentFilters = <?php echo json_encode($currentFilters ?? []); ?>;
        
        // Error handling
        const hasError = <?php echo json_encode(isset($error)); ?>;
        const errorMessage = <?php echo json_encode($error ?? ''); ?>;

        // Shopping cart - now handled by cart-api.js
        // let cart = []; // Removed - using database cart


        // Render products
        function renderProducts(productsToRender = products) {
            const grid = document.getElementById('products-grid');
            
            // Handle error state
            if (hasError) {
                grid.innerHTML = `
                    <div class="loading-state">
                        <div style="font-size: 4rem; color: var(--danger-color); margin-bottom: 1rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Error Loading Products</h3>
                        <p>${errorMessage}</p>
                        <button class="btn-primary" onclick="location.reload()">Retry</button>
                    </div>
                `;
                return;
            }
            
            if (!productsToRender || productsToRender.length === 0) {
                grid.innerHTML = `
                    <div class="loading-state">
                        <div style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>No products found</h3>
                        <p>Try adjusting your search criteria or filters.</p>
                    </div>
                `;
                return;
            }

            const productCards = productsToRender.map(product => {
                const stars = generateStars(product.rating);
                const discountPercent = product.original_price 
                    ? Math.round(((product.original_price - product.price) / product.original_price) * 100)
                    : 0;

                // Determine badge
                let badge = '';
                let badgeClass = '';
                if (product.original_price && discountPercent > 0) {
                    badge = `${discountPercent}% OFF`;
                    badgeClass = 'sale';
                } else if (product.is_featured) {
                    badge = 'FEATURED';
                    badgeClass = 'new';
                }

                // Get category info
                const categorySlug = product.category_slug || 'general';
                const categoryName = product.category_name || 'General';

                return `
                    <div class="product-card">
                        <div class="product-image">
                            ${badge ? `<div class="product-badge ${badgeClass}">${badge}</div>` : ''}
                            ${getProductImage(product, categorySlug)}
                            <div class="product-icon" style="display: none;">${getCategoryIcon(categorySlug)}</div>
                            <div class="product-overlay">
                                <button class="quick-view-btn" onclick="viewProduct(${product.id})">
                                    <i class="fas fa-eye"></i>
                                    Quick View
                                </button>
                            </div>
                        </div>
                        <div class="product-content">
                            <div class="product-category">${categoryName.toUpperCase()}</div>
                            <h3 class="product-name">${product.name}</h3>
                            <p class="product-description">${product.short_description || product.description}</p>
                            <div class="product-rating">
                                <div class="stars">${stars}</div>
                                <span class="rating-text">(${product.review_count} reviews)</span>
                            </div>
                            <div class="product-price">
                                <span class="price-current">LKR ${parseFloat(product.price).toLocaleString()}</span>
                                ${product.original_price ? `<span class="price-original">LKR ${parseFloat(product.original_price).toLocaleString()}</span>` : ''}
                                ${discountPercent > 0 ? `<span class="price-discount">${discountPercent}% OFF</span>` : ''}
                            </div>
                            <div class="product-actions">
                                <button class="btn-outline" onclick="viewProduct(${product.id})">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </button>
                                <button class="btn-primary" onclick="addToCart(${product.id})" ${product.stock_quantity <= 0 ? 'disabled' : ''}>
                                    <i class="fas fa-cart-plus"></i>
                                    ${product.stock_quantity > 0 ? 'Add to Cart' : 'Out of Stock'}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            grid.innerHTML = productCards;
        }

        // Get product image based on product data or category
        function getProductImage(product, categorySlug) {
            // Check if product has specific images
            if (product.images && product.images.length > 0) {
                try {
                    const images = typeof product.images === 'string' ? JSON.parse(product.images) : product.images;
                    if (Array.isArray(images) && images.length > 0 && images[0]) {
                        return `<img src="${images[0]}" alt="${product.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">`;
                    }
                } catch (e) {
                    // Silent error handling
                }
            }

            // Use category-specific default images
            const productImages = {
                'football': '/public/assets/images/products/football.jpg',
                'tennis': '/public/assets/images/products/tennis-racket.jpg',
                'basketball': '/public/assets/images/products/basketball.jpg',
                'cricket': '/public/assets/images/products/cricket-bat.jpg',
                'badminton': '/public/assets/images/products/badminton-racket.jpg',
                'swimming': '/public/assets/images/products/football.jpg',
                'fitness': '/public/assets/images/products/football.jpg'
            };

            const imageUrl = productImages[categorySlug] || '/public/assets/images/products/football.jpg';
            return `<img src="${imageUrl}" alt="${product.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">`;
        }

        // Generate star rating HTML
        function generateStars(rating) {
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 !== 0;
            
            for (let i = 0; i < fullStars; i++) {
                stars += '<i class="fas fa-star"></i>';
            }
            
            if (hasHalfStar) {
                stars += '<i class="fas fa-star-half-alt"></i>';
            }
            
            const remainingStars = 5 - Math.ceil(rating);
            for (let i = 0; i < remainingStars; i++) {
                stars += '<i class="far fa-star"></i>';
            }
            
            return stars;
        }

        // Render categories
        function renderCategories() {
            const grid = document.getElementById('categories-grid');
            
            if (!categories || categories.length === 0) {
                grid.innerHTML = '<p>No categories available</p>';
                return;
            }

            // Filter out categories with 0 products
            const categoriesWithProducts = categories.filter(category => 
                category.product_count && category.product_count > 0
            );

            if (categoriesWithProducts.length === 0) {
                grid.innerHTML = '<p>No categories with products available</p>';
                return;
            }

            const categoryItems = categoriesWithProducts.map(category => {
                return `
                    <a href="#" class="category-item" data-category="${category.slug}" onclick="filterByCategory('${category.slug}')">
                        <div class="category-icon">${category.icon || '<i class="fas fa-map-marker-alt"></i>'}</div>
                        <div class="category-name">${category.name}</div>
                        <div class="category-count">${category.product_count} items</div>
                    </a>
                `;
            }).join('');

            grid.innerHTML = categoryItems;
        }

        // Filter products by category
        function filterByCategory(categorySlug) {
            if (categorySlug === 'all' || !categorySlug) {
                renderProducts(products);
            } else {
                const filteredProducts = products.filter(product => 
                    product.category_slug === categorySlug
                );
                renderProducts(filteredProducts);
            }
        }

        // Get category icon
        function getCategoryIcon(category) {
            const icons = {
                'football': '<i class="fas fa-futbol"></i>',
                'tennis': '<i class="fas fa-baseball-ball"></i>',
                'basketball': '<i class="fas fa-basketball-ball"></i>',
                'cricket': '<i class="fas fa-baseball-ball"></i>',
                'badminton': '<i class="fas fa-baseball-ball"></i>',
                'swimming': '<i class="fas fa-swimmer"></i>',
                'fitness': '<i class="fas fa-dumbbell"></i>'
            };
            return icons[category] || '<i class="fas fa-running"></i>';
        }

        // Add to cart - now handled by cart-api.js
        // function addToCart(productId) { ... } // Removed - using API

        // View product details
        function viewProduct(productId) {
            // Redirect to product details page
            window.location.href = `${window.BASE_URL||''}/product/${productId}`;
        }


        // Cart functions now handled by cart-api.js
        // updateCartDisplay(), toggleCart(), showCartSummary(), checkout() moved to cart-api.js

        /* Toggle cart summary
        function toggleCart() {
            const cartSummary = document.getElementById('cart-summary');
            cartSummary.classList.toggle('visible');
        }

        // Show cart summary temporarily
        function showCartSummary() {
            const cartSummary = document.getElementById('cart-summary');
            cartSummary.classList.add('visible');
            setTimeout(() => {
                cartSummary.classList.remove('visible');
            }, 3000);
        }

        // Checkout
        function checkout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            // Redirect to checkout page
            window.location.href=(window.BASE_URL||'')+'/checkout/contact-details';
        }*/


        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            
            // Render categories and products from database
            if (!hasError) {
                renderCategories();
                renderProducts();
            } else {
                renderProducts(); // Will show error state
            }
        });


// --- Header filter helpers -----------------------------

function parsePriceRange(val) {
  // Map UI values to LKR ranges in your labels
  // "" => no limit, "0-50" => 0..5000, "50-100" => 5000..10000, "100+" => 10000..∞
  if (!val) return {min:null, max:null};
  if (val === '0-50')   return {min:0,     max:5000};
  if (val === '50-100') return {min:5000,  max:10000};
  if (val === '100+')   return {min:10000, max:null};
  return {min:null, max:null};
}

function applyHeaderFilters() {
  const qInput      = document.getElementById('search-products');
  const catSelect   = document.getElementById('category-filter');
  const priceSelect = document.getElementById('price-filter');
  const sortSelect  = document.querySelector('.sort-select'); // has class in your markup

  const q   = (qInput?.value || '').trim().toLowerCase();
  const cat = (catSelect?.value || '').trim();            // category slug
  const {min, max} = parsePriceRange(priceSelect?.value || '');
  const sort = (sortSelect?.value || 'featured');

  // filter
  let filtered = products.filter(p => {
    // category
    if (cat && p.category_slug !== cat) return false;

    // search (name or description)
    if (q) {
      const blob = ((p.name || '') + ' ' + (p.short_description || p.description || '')).toLowerCase();
      if (!blob.includes(q)) return false;
    }

    // price
    const price = Number(p.price || 0);
    if (min !== null && price < min) return false;
    if (max !== null && price > max) return false;

    // only active (your PHP already sends active, keep guard)
    if (p.status && p.status !== 'active') return false;

    return true;
  });

  // sort
  const sorters = {
    'featured': (a,b) => (b.rating ?? 0) - (a.rating ?? 0) || new Date(b.created_at) - new Date(a.created_at),
    'price-low': (a,b) => (a.price ?? 0) - (b.price ?? 0),
    'price-high': (a,b) => (b.price ?? 0) - (a.price ?? 0),
    'rating': (a,b) => (b.rating ?? 0) - (a.rating ?? 0),
    'newest': (a,b) => new Date(b.created_at) - new Date(a.created_at),
    'name': (a,b) => String(a.name||'').localeCompare(String(b.name||''))
  };
  filtered.sort(sorters[sort] || sorters['featured']);

  renderProducts(filtered);
}

// Keep category grid click in sync with header select
function filterByCategory(categorySlug) {
  const catSelect = document.getElementById('category-filter');
  if (catSelect) {
    catSelect.value = categorySlug || '';
  }
  applyHeaderFilters();
  return false;
}

// --- Wire up events ------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  // First render from DB
  if (!hasError) {
    renderCategories();
    renderProducts();
  } else {
    renderProducts();
  }

  const qInput      = document.getElementById('search-products');
  const catSelect   = document.getElementById('category-filter');
  const priceSelect = document.getElementById('price-filter');
  const sortSelect  = document.querySelector('.sort-select');
  const searchBtn   = document.querySelector('.search-btn');

  // trigger on interactions
  qInput?.addEventListener('keydown', e => { if (e.key === 'Enter') applyHeaderFilters(); });
  catSelect?.addEventListener('change', applyHeaderFilters);
  priceSelect?.addEventListener('change', applyHeaderFilters);
  sortSelect?.addEventListener('change', applyHeaderFilters);
  searchBtn?.addEventListener('click', applyHeaderFilters);
});

    </script>
