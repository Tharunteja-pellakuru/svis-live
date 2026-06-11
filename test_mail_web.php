<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SVIS SMTP Mail Tester</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --surface: #1e293b;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --text: #f8fafc;
            --text-secondary: #94a3b8;
            --border: #334155;
            --success: #10b981;
            --error: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 28px;
            font-weight: 800;
            margin-top: 0;
            margin-bottom: 8px;
            background: linear-gradient(to right, #60a5fa, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            color: var(--text-secondary);
            font-size: 15px;
            margin-bottom: 30px;
        }

        .btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            display: inline-flex;
            align-items: center;
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .result-box {
            margin-top: 30px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .result-header {
            padding: 12px 20px;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-success {
            background-color: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border-bottom: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-error {
            background-color: rgba(239, 68, 68, 0.15);
            color: var(--error);
            border-bottom: 1px solid rgba(239, 68, 68, 0.3);
        }

        pre {
            background-color: #0b0f19;
            color: #38bdf8;
            padding: 20px;
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            line-height: 1.5;
            overflow-x: auto;
            max-height: 450px;
            overflow-y: auto;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .info-card {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            padding: 16px;
            border-radius: 8px;
        }

        .info-label {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 700;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SMTP Mail Server Diagnostic</h1>
        <p class="subtitle">Use this page to check SMTP settings, test connectivity, and view detailed debugging output.</p>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">SMTP Host</div>
                <div class="info-value"><?php echo SMTP_HOST; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">SMTP Port</div>
                <div class="info-value"><?php echo SMTP_PORT; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">SMTP Username</div>
                <div class="info-value"><?php echo SMTP_USER; ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">Sender Email</div>
                <div class="info-value"><?php echo SMTP_FROM; ?></div>
            </div>
        </div>

        <form method="POST">
            <button type="submit" name="run_test" class="btn">Run SMTP Diagnostic Test</button>
        </form>

        <?php
        if (isset($_POST['run_test'])) {

            try {
                $subject = "SVIS Production Diagnostic Test";
                $body    = "This is a diagnostic test email to check if the production server can successfully talk to the Brevo API gateway.";
                
                $sent = sendBrevoEmail(SMTP_FROM, "Test User", $subject, $body);
                
                if ($sent) {
                    echo '<div class="result-box">';
                    echo '  <div class="result-header status-success"><span>✔ SUCCESS: Test Email Dispatched Successfully via Brevo HTTP API!</span></div>';
                    echo '  <pre>Response: HTTP 201 Created (Email Sent)</pre>';
                    echo '</div>';
                } else {
                    throw new \Exception("Brevo HTTP API request failed. Check your API key and sender email.");
                }
            } catch (\Exception $e) {
                echo '<div class="result-box">';
                echo '  <div class="result-header status-error"><span>✘ FAILURE: ' . htmlspecialchars($e->getMessage()) . '</span></div>';
                echo '  <pre>Could not deliver via API relay.</pre>';
                echo '</div>';
            }
        }
        ?>
    </div>
</body>
</html>
