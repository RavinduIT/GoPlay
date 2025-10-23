<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ground Owner Settings</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7f9fb;
            margin: 0;
            padding: 0;
        }

        .settings-container {
            max-width: 600px;
            background: #fff;
            margin: 60px auto;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 26px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #444;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: border 0.2s;
        }

        input:focus {
            border-color: #007bff;
        }

        .btn-save {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 18px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s;
        }

        .btn-save:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <h2>Ground Owner Settings</h2>

        <form method="POST" action="#">
            <div class="form-group">
                <label>Ground Name</label>
                <input type="text" name="name" value="Sunrise Sports Arena">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="sunrisearena@gmail.com">
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="0774567890">
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" value="Colombo, Sri Lanka">
            </div>

            <div class="form-group">
                <label>Operating Hours</label>
                <input type="text" name="hours" value="6:00 AM - 10:00 PM">
            </div>

            <button type="submit" class="btn-save">Save Changes</button>
        </form>
    </div>
</body>
</html>
