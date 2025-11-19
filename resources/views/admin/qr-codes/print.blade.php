<!DOCTYPE html>
<html>
<head>
    <title>Print QR Code - {{ $qrCode->code }}</title>
    <style>
        @page { size: A4; margin: 2cm; }
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        .qr-container { margin: 50px auto; }
        .qr-code { max-width: 600px; margin: 20px auto; }
        .code-text { font-size: 24px; font-weight: bold; margin: 20px 0; }
        .info { font-size: 16px; margin: 10px 0; color: #666; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <h1>Attendance QR Code</h1>

        <div class="qr-code">
            {!! $qrCodeSvg !!}
        </div>
        
        <div class="code-text">{{ $qrCode->code }}</div>
        
        <div class="info">
            <p>Type: {{ ucfirst($qrCode->type) }}</p>
            @if($qrCode->valid_from)
                <p>Valid From: {{ $qrCode->valid_from->format('M d, Y') }}</p>
            @endif
            @if($qrCode->valid_until)
                <p>Valid Until: {{ $qrCode->valid_until->format('M d, Y') }}</p>
            @endif
        </div>
        
        <p style="margin-top: 40px; font-size: 14px; color: #999;">
            Scan this code with your mobile device to mark attendance
        </p>
    </div>
    
    <div class="no-print" style="margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #10B981; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px;">
            Print QR Code
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6B7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-left: 10px;">
            Close
        </button>
    </div>
</body>
</html>
