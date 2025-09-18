<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Dashboard - Falcon Camps</title>
    <style>
        /* Add your existing styles here */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #6e84e7 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            background: #0a3f94;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .dashboard-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Coach Dashboard</h1>


            <div style="margin-top: 20px;">
                <a href="{{ route('home') }}" style="color: white; margin-right: 15px;">← Back to Home</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </header>

        <div class="dashboard-content">
            <h2>Camp Registrations</h2>
            <p><em>Registration data will appear here once you implement the database storage.</em></p>

            <!-- Placeholder for future registration data -->
            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <h3>Coming Soon:</h3>
                <ul>
                    <li>View all camp registrations</li>
                    <li>Filter by specific camps</li>
                    <li>Export registration data</li>
                    <li>Contact information for parents</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
