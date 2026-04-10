<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupon Terms & Conditions | PS GDC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #D32F2F;
            --primary-light: #FFEBEE;
            --text-main: #1A1D1E;
            --text-muted: #64748B;
            --bg: #F8FAFC;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: var(--primary-light);
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: bold;
            font-size: 24px;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .updated {
            font-size: 14px;
            color: var(--text-muted);
        }

        .section {
            margin-bottom: 32px;
        }

        h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        p {
            font-size: 15px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        ul {
            list-style: none;
            margin-bottom: 16px;
        }

        li {
            font-size: 15px;
            color: var(--text-muted);
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        li::before {
            content: "•";
            color: var(--primary);
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
        }

        @media (max-width: 480px) {
            .container {
                padding: 24px;
                margin: 20px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo-placeholder">PSG</div>
            <h1>Coupon Terms</h1>
            <p class="updated">Last Updated: April 10, 2026</p>
        </header>

        <div class="section">
            <h2>General Rules</h2>
            <p>By using a coupon or voucher code on the PS GDC platform, you agree to these terms:</p>
            <ul>
                <li>Coupons are only valid for orders placed through the PS GDC mobile application.</li>
                <li>Each coupon can only be used once per customer unless otherwise stated.</li>
                <li>Coupons cannot be combined with other promotional offers or discounts.</li>
            </ul>
        </div>

        <div class="section">
            <h2>Exclusions</h2>
            <ul>
                <li>Some high-demand pharmaceuticals or limited stock items may be excluded from coupon discounts.</li>
                <li>Delivery fees and service charges are not eligible for discount unless specified.</li>
            </ul>
        </div>

        <div class="section">
            <h2>Expiration & Validity</h2>
            <ul>
                <li>Coupons have fixed expiration dates. Codes will not function after the designated end date.</li>
                <li>PS GDC reserves the right to modify or cancel any promotion at any time without prior notice.</li>
            </ul>
        </div>

        <div class="footer">
            &copy; 2026 PS General Drug Centre. All rights reserved.
        </div>
    </div>
</body>
</html>
