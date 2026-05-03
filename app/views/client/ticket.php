<?php
$eSnap = !empty($booking['event_snapshot']) ? json_decode($booking['event_snapshot'], true) : [];
$pSnap = !empty($booking['package_snapshot']) ? json_decode($booking['package_snapshot'], true) : [];

$eventTitle = $eSnap['title'] ?? ($booking['event_title'] ?? 'Concert');
$eventDate = !empty($booking['event_date']) ? date('M d, Y', strtotime($booking['event_date'])) : 'TBD';
$eventTime = !empty($booking['checkin_time']) ? date('h:i A', strtotime($booking['checkin_time'])) : '06:00 PM';
$venueName = $eSnap['venue_name'] ?? ($booking['venue_name'] ?: 'Venue TBD');
$venueLocation = $eSnap['venue_location'] ?? ($booking['venue_location'] ?: 'Kathmandu');

$ticketId = "TICK-" . str_pad($booking['id'], 6, '0', STR_PAD_LEFT);
$clientName = $booking['full_name'] ?? 'Guest';
$tier = strtoupper($booking['package_tier'] ?? 'GENERAL');
$ticketCount = $booking['guest_count'] ?? 1;

$qrData = urlencode("TICKET:$ticketId|USER:$clientName|EVENT:$eventTitle");
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=$qrData";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket - <?php echo htmlspecialchars($eventTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Libre+Barcode+128&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #246A55;
            --accent: #FFC24A;
            --bg: #f8fafc;
        }
        body {
            margin: 0;
            padding: 40px;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            display: flex;
            justify-content: center;
        }
        .ticket-container {
            width: 800px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            position: relative;
        }
        /* Stub Notch Effect */
        .ticket-container::before, .ticket-container::after {
            content: '';
            position: absolute;
            left: 580px;
            width: 30px;
            height: 30px;
            background: var(--bg);
            border-radius: 50%;
            z-index: 10;
        }
        .ticket-container::before { top: -15px; }
        .ticket-container::after { bottom: -15px; }

        .ticket-header {
            background: var(--primary);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-weight: 800; font-size: 24px; letter-spacing: -1px; }
        .tier-tag {
            background: var(--accent);
            color: #1a1e23;
            padding: 6px 16px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
        }

        .ticket-body {
            display: flex;
            padding: 0;
        }
        .main-info {
            flex: 1;
            padding: 40px;
            border-right: 2px dashed #e2e8f0;
            position: relative;
        }
        .stub {
            width: 220px;
            padding: 40px 20px;
            background: #fafafa;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        h1 { margin: 0; color: #1e293b; font-size: 32px; font-weight: 800; line-height: 1.1; }
        .category { color: var(--primary); font-weight: 600; font-size: 14px; margin-bottom: 8px; display: block; }
        
        .details-grid {
            margin-top: 35px;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 25px;
        }
        .detail-item label {
            display: block;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .detail-item span {
            display: block;
            font-size: 18px;
            color: #1e293b;
            font-weight: 600;
        }

        .qr-section { margin-top: 0; }
        .qr-section img { width: 140px; height: 140px; margin-bottom: 15px; }
        .ticket-id { font-family: 'Libre Barcode 128', cursive; font-size: 40px; margin-top: 20px; color: #1e293b; }
        .ticket-id-text { font-size: 12px; color: #94a3b8; font-weight: 600; font-family: monospace; }

        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(36, 106, 85, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 100;
        }

        @media print {
            body { padding: 0; background: white; }
            .ticket-container { box-shadow: none; border: 1px solid #eee; margin: 0 auto; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>

    <a href="javascript:window.print()" class="print-btn">
        <span>Print Ticket</span>
    </a>

    <div class="ticket-container">
        <div class="ticket-header">
            <div class="logo">e.PLAN</div>
            <div class="tier-tag"><?php echo htmlspecialchars($tier); ?> ADMIT</div>
        </div>
        <div class="ticket-body">
            <div class="main-info">
                <span class="category">CONCERT ADMISSION</span>
                <h1><?php echo htmlspecialchars($eventTitle); ?></h1>
                
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Date & Time</label>
                        <span><?php echo $eventDate; ?> @ <?php echo $eventTime; ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Tickets</label>
                        <span><?php echo $ticketCount; ?> Person(s)</span>
                    </div>
                    <div class="detail-item">
                        <label>Venue</label>
                        <span><?php echo htmlspecialchars($venueName); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Holder</label>
                        <span><?php echo htmlspecialchars($clientName); ?></span>
                    </div>
                </div>

                <div style="margin-top: 40px; font-size: 11px; color: #94a3b8; line-height: 1.4;">
                    * This ticket is non-transferable. Please present this ticket at the entrance. <br>
                    * Gates open 30 minutes before the event starts.
                </div>
            </div>
            <div class="stub">
                <div class="qr-section">
                    <img src="<?php echo $qrUrl; ?>" alt="QR Code">
                </div>
                <div class="ticket-id-text"><?php echo $ticketId; ?></div>
                <div class="ticket-id"><?php echo $ticketId; ?></div>
                <div style="margin-top: auto; font-size: 10px; color: #94a3b8; font-weight: 600;">
                    SECURE ENTRY CODE
                </div>
            </div>
        </div>
    </div>

</body>
</html>
