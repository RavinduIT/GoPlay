<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($coach['first_name'] . ' ' . $coach['last_name']) ?> - Coach Profile | GoPlay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #0891b2;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;
            --background-light: #f9fafb;
            --background-white: #ffffff;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: var(--background-light);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: var(--transition);
            justify-content: center;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Profile Header */
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: var(--border-radius);
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path fill="rgba(255,255,255,0.1)" d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.1;
        }

        .profile-header-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: var(--shadow-lg);
            object-fit: cover;
            background: var(--background-white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .profile-sport {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .profile-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .profile-badges {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
            align-items: start;
        }

        .card {
            background: var(--background-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 2rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-primary);
        }

        .card-title i {
            color: var(--primary-color);
        }

        /* About Section */
        .about-text {
            color: var(--text-secondary);
            line-height: 1.8;
        }

        .specializations {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .specialization-tag {
            background: var(--primary-light);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        /* Info List */
        .info-list {
            list-style: none;
        }

        .info-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Certifications */
        .certifications-list {
            list-style: none;
        }

        .certification-item {
            background: var(--background-light);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .certification-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .certification-content {
            flex: 1;
        }

        .certification-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .certification-date {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        /* Reviews */
        .reviews-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .review-item {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .reviewer-name {
            font-weight: 600;
        }

        .review-rating {
            color: var(--warning-color);
            display: flex;
            gap: 0.25rem;
        }

        .review-text {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .review-date {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-top: 0.5rem;
        }

        .no-reviews {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }

        /* Booking Card */
        .booking-card {
            position: sticky;
            top: 80px;
            align-self: flex-start;
            z-index: 10;
        }

        .price-display {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .price-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .btn-book {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            margin-top: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-header-content {
                flex-direction: column;
                text-align: center;
            }

            .profile-stats {
                justify-content: center;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .booking-card {
                position: sticky;
                bottom: 0;
                top: auto;
                margin-top: 2rem;
                box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1), 0 -2px 4px -1px rgba(0, 0, 0, 0.06);
            }
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    <?php if (!empty($coach['profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($coach['profile_picture']) ?>" alt="<?= htmlspecialchars($coach['first_name']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <?= strtoupper(substr($coach['first_name'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name">
                        <?= htmlspecialchars($coach['first_name'] . ' ' . $coach['last_name']) ?>
                    </h1>
                    <div class="profile-sport">
                        <i class="fas fa-<?= htmlspecialchars($coach['sport_icon'] ?? 'trophy') ?>"></i>
                        <?= htmlspecialchars($coach['sport_name']) ?> Coach
                    </div>
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?= number_format($coach['rating'], 1) ?> <i class="fas fa-star"></i></span>
                            <span class="stat-label">Rating</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= $coach['total_reviews'] ?></span>
                            <span class="stat-label">Reviews</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= $coach['completed_sessions'] ?></span>
                            <span class="stat-label">Sessions</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= $coach['experience_years'] ?> Years</span>
                            <span class="stat-label">Experience</span>
                        </div>
                    </div>
                    <div class="profile-badges">
                        <span class="badge">
                            <i class="fas fa-check-circle"></i> Verified Coach
                        </span>
                        <?php if ($coach['rating'] >= 4.5): ?>
                        <span class="badge">
                            <i class="fas fa-star"></i> Top Rated
                        </span>
                        <?php endif; ?>
                        <span class="badge">
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($coach['location']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Left Column -->
            <div>
                <!-- About Section -->
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-user-circle"></i>
                        About
                    </h2>
                    <p class="about-text">
                        <?= nl2br(htmlspecialchars($coach['bio'])) ?>
                    </p>
                    
                    <?php if (!empty($coach['specializations'])): ?>
                    <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 1.1rem;">Specializations</h3>
                    <div class="specializations">
                        <?php 
                        $specializations = explode(',', $coach['specializations']);
                        foreach ($specializations as $spec): 
                        ?>
                            <span class="specialization-tag"><?= htmlspecialchars(trim($spec)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Certifications -->
                <?php if (!empty($coach['certifications'])): ?>
                <div class="card" style="margin-top: 2rem;">
                    <h2 class="card-title">
                        <i class="fas fa-certificate"></i>
                        Certifications & Qualifications
                    </h2>
                    <ul class="certifications-list">
                        <?php 
                        $certifications = explode(',', $coach['certifications']);
                        foreach ($certifications as $cert): 
                        ?>
                            <li class="certification-item">
                                <div class="certification-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="certification-content">
                                    <div class="certification-name"><?= htmlspecialchars(trim($cert)) ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Reviews Section -->
                <div class="card" style="margin-top: 2rem;">
                    <h2 class="card-title">
                        <i class="fas fa-star"></i>
                        Reviews (<?= count($coach['reviews_list']) ?>)
                    </h2>
                    <?php if (!empty($coach['reviews_list'])): ?>
                        <div class="reviews-list">
                            <?php foreach ($coach['reviews_list'] as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <div class="reviewer-avatar">
                                                <?= strtoupper(substr($review['first_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="reviewer-name">
                                                    <?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="review-rating">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <i class="fas fa-star<?= $i < $review['rating'] ? '' : '-o' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>
                                    <div class="review-date">
                                        <?= date('F j, Y', strtotime($review['created_at'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-reviews">
                            <i class="fas fa-comments" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p>No reviews yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column (Sidebar) -->
            <div>
                <!-- Booking Card -->
                <div class="card booking-card">
                    <h2 class="card-title">
                        <i class="fas fa-calendar-check"></i>
                        Book a Session
                    </h2>
                    <div class="price-display">
                        LKR <?= number_format($coach['hourly_rate'], 2) ?>
                        <span class="price-label">/ session</span>
                    </div>
                    
                    <a href="/app/views/booking/book-session.php?coach_id=<?= $coach['id'] ?>" class="btn-primary btn-book">
                        <i class="fas fa-calendar-plus"></i>
                        Book Now
                    </a>
                </div>

                <!-- Coach Details Card -->
                <div class="card" style="margin-top: 2rem;">
                    <h2 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Coach Details
                    </h2>
                    <ul class="info-list">
                        <li class="info-item">
                            <span class="info-label">
                                <i class="fas fa-trophy"></i>
                                Sport
                            </span>
                            <span class="info-value"><?= htmlspecialchars($coach['sport_name']) ?></span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">
                                <i class="fas fa-briefcase"></i>
                                Experience
                            </span>
                            <span class="info-value"><?= $coach['experience_years'] ?> Years</span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Location
                            </span>
                            <span class="info-value"><?= htmlspecialchars($coach['location']) ?></span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">
                                <i class="fas fa-users"></i>
                                Total Sessions
                            </span>
                            <span class="info-value"><?= $coach['completed_sessions'] ?></span>
                        </li>
                        <li class="info-item">
                            <span class="info-label">
                                <i class="fas fa-star"></i>
                                Average Rating
                            </span>
                            <span class="info-value"><?= number_format($coach['rating'], 1) ?> / 5.0</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
