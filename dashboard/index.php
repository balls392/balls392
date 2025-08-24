<?php
include '../credentials.php';
require '../keyauth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['un'])) {
    die("not logged in");
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../");
    exit();
}

// Initialize KeyAuth API
$KeyAuthApp = new KeyAuth\api($name, $OwnerId);

// Fetch application settings from KeyAuth seller API
$url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=getsettings";
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($curl);
$json = json_decode($resp);

if (!$json->success) {
    die("Error: {$json->message}");
}

// Get the webloader download link from the API response
$webdownload = $json->webdownload ?? null;

// Fetch user data from KeyAuth seller API
$un = $_SESSION['un'];
$url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=userdata&user={$un}";
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($curl);
$user_json = json_decode($resp);

if (!$user_json->success) {
    die("Error: {$user_json->message}");
}

$user_details = [
    'username' => $user_json->username ?? 'N/A',
    'subscription' => $user_json->subscriptions[0]->subscription ?? 'N/A',
    'expiry' => $user_json->subscriptions[0]->expiry ?? 'N/A',
    'token' => $user_json->token ?? 'N/A',
];

$status_message = $_SESSION['status_message'] ?? null;
unset($_SESSION['status_message']);
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <title>FNP Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <style>
        :root {
            --bg-color: #1a1a31;
            --panel-color: #121223;
            --text-color: #ffffff;
            --secondary-text-color: #b2b9bf;
            --primary-accent: #7f52e3;
            --primary-accent-hover: #5d3ea6;
            --red-text-color: #ff3333;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: var(--panel-color);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid rgba(255, 255, 255, 0.05);
        }

        .header h1 {
            margin: 0;
            font-size: 1.5em;
        }

        .logout-form button {
            background-color: transparent;
            color: var(--text-color);
            border: none;
            cursor: pointer;
            font-size: 1em;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .logout-form button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .panel {
            background-color: var(--panel-color);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .panel-heading {
            font-size: 1.4em;
            color: var(--text-color);
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .info-item {
            margin-bottom: 12px;
            color: var(--secondary-text-color);
            width: 100%;
            text-align: left;
        }

        .info-label {
            color: var(--text-color);
            font-weight: bold;
            min-width: 120px;
            display: inline-block;
        }
        
        .user-details-list {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .download-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        .button {
            background-color: var(--primary-accent);
            color: var(--text-color);
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
            width: 100%;
            max-width: 300px;
        }

        .button:hover {
            background-color: var(--primary-accent-hover);
            transform: translateY(-2px);
        }
        
        .instructions-panel {
            background-color: var(--panel-color);
            border-radius: 12px;
            padding: 20px 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }

        .instructions-panel .panel-heading {
            text-align: center;
        }

        .instructions-list {
            text-align: left;
            margin: 0;
            padding-left: 20px;
            list-style-type: decimal;
        }
        
        .instructions-list li {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .red-text {
            color: var(--red-text-color);
            font-size: 0.9em;
            font-style: italic;
            display: block;
            margin-top: 5px;
        }

        .status-message {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
            color: #38c172;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #888;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>FNP Panel</h1>
    <form method="post" class="logout-form">
        <button type="submit" name="logout">
            <i class="fa-solid fa-right-from-bracket"></i> Log out
        </button>
    </form>
</div>

<?php if ($status_message): ?>
    <div class="status-message"><?php echo htmlspecialchars($status_message); ?></div>
<?php endif; ?>

<div class="main-content">
    <div class="dashboard-grid">
        <div class="panel">
            <h2 class="panel-heading">User Details</h2>
            <div class="user-details-list">
                <div class="info-item">
                    <span class="info-label">Username:</span> <?php echo $user_details['username']; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Subscription:</span> <?php echo $user_details['subscription']; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">License Expiry:</span>
                    <span class="expiry-date"><?php echo date("d/m/Y, H:i:s", $user_details['expiry']); ?></span>
                </div>
            </div>
        </div>
        
        <div class="panel">
            <h2 class="panel-heading">Downloads</h2>
            <div class="download-links">
                <?php if ($webdownload): ?>
                    <a href="<?php echo $webdownload; ?>" class="button" target="_blank">Download Loader</a>
                <?php else: ?>
                    <a href="https://example.com/fakeloader" class="button" target="_blank">Download Loader</a>
                <?php endif; ?>
                <a href="https://steelseries.com/gg?srsltid=AfmBOoq19umg1fEnDQHdHvaagBgaJVAfPifvMOIvH-oZX56VtbTSdR7z" class="button" target="_blank">Download SteelSeriesGG</a>
                <a href="https://example.com/terminatetool" class="button" target="_blank">Download Terminate Tool</a>
            </div>
        </div>
    </div>
    
    <div class="instructions-panel">
        <h2 class="panel-heading">Instructions</h2>
        <ol class="instructions-list">
            <li>Download <b>SteelSeriesGG</b> and the <b>Loader</b> from the links above.</li>
            <li>In SteelSeriesGG, go to the <b>Moments</b> tab. Disable <b>Sonar</b> and <b>Auto-Clipping</b>, but keep <b>recording enabled</b>.</li>
            <li>Run the loader and enter your <b>license key</b>.</li>
            <li>If needed, you can spoof your serials in the loader by clicking the <b>Spoof Serials</b> button.</li>
            <li>Click <b>Start Fortnite External</b> in the loader.</li>
            <li>Once the loader says "Start Desktop Capture," return to the <b>Moments</b> tab in SteelSeries and click "Waiting for game to capture." A list of applications will appear; click <b>"Start Desktop Capture"</b>.</li>
            <li>When the loader says "Start Fortnite," open your game.</li>
            <li>Once you are in the lobby, double-click the <b>right shift</b> key, and the menu should pop up.</li>
            <li><b>OPTIONAL:</b> After the loader is injected, you can stop the desktop recording in SteelSeriesGG to save resources.</li>
        </ol>
    </div>
</div>

<div class="footer">
    Copyright © 2024-<?php echo date("Y"); ?> · FNP
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

</body>
</html>