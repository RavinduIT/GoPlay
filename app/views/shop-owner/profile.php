<?php 
$title = 'Profile - GoPlay';
?>

<div class="shop-owner-dashboard">

  <!-- Sidebar include -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="dashboard-content">
    <header class="profile-header">
      <h1>Profile</h1>
    </header>

    <section class="profile-wrapper">
      <!-- Left Card: Profile Info -->
      <div class="profile-card info-card">
        <form action="update_profile.php" method="post" enctype="multipart/form-data" id="profile-form">
          <div class="profile-fields">
            <div class="field">
              <label>Shop Name:</label>
              <input type="text" name="shop_name" value="GoPlay Sports Shop">
            </div>

            <div class="field">
              <label>Owner Name:</label>
              <input type="text" name="owner_name" value="Rohan Gunaratne">
            </div>

            <div class="field">
              <label>Email:</label>
              <input type="email" name="email" value="rohan@wxampie.com">
            </div>

            <div class="field">
              <label>Address:</label>
              <input type="text" name="address" value="No. 45, Main Street, Colombo">
            </div>

            <div class="field">
              <label>Telephone:</label>
              <input type="text" name="telephone" value="+94 77 123 4567">
            </div>
          </div>

          <!-- Bottom Edit Button -->
          <div class="profile-actions">
            <button type="submit" class="btn-edit">Edit Profile</button>
          </div>
        </form>
      </div>

      <!-- Right Card: Profile Photo -->
      <div class="profile-card photo-card">
        <div class="profile-photo-section">
          <img src="/public/assets/images/default-avatar.png" alt="Profile Photo" class="profile-photo"></div>
          <div class="btn-upload">
            Edit Photo
            <input type="file" name="profile_photo" accept="image/*" hidden>
          </div>
        
      </div>
    </section>
  </main>
</div>


<style>
/* ---- Layout base ---- */
.shop-owner-dashboard { display: flex; min-height: 100vh; }
.dashboard-content { flex: 1; margin-left: 280px; padding: 20px; background: #f9f9f9; }

/* Header */
.profile-header { margin-bottom: 1.5rem; }

/* ---- Profile Wrapper ---- */
.profile-wrapper {
  display: flex;
  gap: 25px;
  align-items: flex-start;
}

/* Cards */
.profile-card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e3e3e3;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.info-card { flex: 2;padding: 25px 25px; }
.photo-card { flex: 1; text-align: center;padding: 50px 25px 80px 25px; }

/* Profile Photo Section */
.profile-photo {
  width: 180px;
  height: 200px;
  border-radius: 80%;
  object-fit: cover;
  border: 3px solid #ddd;
}
.btn-upload {
  display: inline-block;
  margin-top: 50px;
  background: #2563eb;
  color: #fff;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
}
.btn-upload:hover { background: #64748b; }

/* Profile Fields */
.profile-fields {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.field {
  display: flex;
  flex-direction: column;
}
.field label {
  font-weight: 600;
  margin-bottom: 6px;
  color: #555454ff;
}
.field input {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 0.95rem;
}

/* Actions */
.profile-actions {
  margin-top: 25px;
  text-align: center;
}
.btn-edit {
  background: #2563eb;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-size: 1rem;
  cursor: pointer;
  font-weight: 600;
}
.btn-edit:hover { background: #64748b; }

/* Responsive */
@media (max-width: 768px) {
  .profile-wrapper { flex-direction: column; }
  .photo-card { order: -1; } /* Move photo above fields on mobile */
}
</style>
