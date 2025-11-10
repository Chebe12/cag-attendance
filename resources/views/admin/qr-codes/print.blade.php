<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Code - {{ $qrCode->code }}</title>
    <style>
        /* A4 Print Styles */
        @page {
            size: A4;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: white;
            color: #000;
        }

        .print-container {
            width: 210mm;
            height: 297mm;
            padding: 20mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            page-break-after: always;
        }

        .qr-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .qr-header h1 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #16a34a;
        }

        .qr-header p {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }

        .qr-code-container {
            text-align: center;
            padding: 40px;
            border: 3px solid #16a34a;
            border-radius: 20px;
            background: #f9fafb;
            margin-bottom: 30px;
        }

        .qr-code-image {
            width: 400px;
            height: 400px;
            margin: 0 auto;
            display: block;
        }

        .qr-info {
            text-align: center;
            margin-top: 30px;
        }

        .qr-code-text {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
            padding: 15px 30px;
            background: #e5e7eb;
            border-radius: 10px;
            display: inline-block;
        }

        .qr-type {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .qr-type-badge {
            display: inline-block;
            padding: 8px 20px;
            background: #16a34a;
            color: white;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .qr-instructions {
            margin-top: 40px;
            padding: 20px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            max-width: 600px;
        }

        .qr-instructions h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #92400e;
        }

        .qr-instructions ol {
            margin-left: 20px;
            color: #78350f;
        }

        .qr-instructions li {
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .qr-footer {
            position: absolute;
            bottom: 20mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: #999;
        }

        /* Print-specific styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                page-break-after: always;
            }
        }

        /* Print button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 30px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background: #15803d;
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 12px 30px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .back-button:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <!-- Print/Back buttons (hidden when printing) -->
    <a href="{{ route('admin.qr-codes.show', $qrCode) }}" class="back-button no-print">← Back</a>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print QR Code</button>

    <!-- A4 Print Container -->
    <div class="print-container">
        <!-- Header -->
        <div class="qr-header">
            <h1>CAG Attendance System</h1>
            <p>Scan this QR code to mark your attendance</p>
        </div>

        <!-- QR Code -->
        <div class="qr-code-container">
            <img src="data:image/png;base64,{{ $qrCodeImage }}" alt="QR Code" class="qr-code-image">
        </div>

        <!-- QR Code Information -->
        <div class="qr-info">
            <div class="qr-code-text">{{ $qrCode->code }}</div>

            <div class="qr-type">
                <span class="qr-type-badge">
                    {{ strtoupper(str_replace('_', ' ', $qrCode->type)) }}
                </span>
            </div>

            @if($qrCode->metadata && isset($qrCode->metadata['label']))
                <p style="font-size: 20px; margin-top: 15px; font-weight: bold; color: #000;">
                    {{ $qrCode->metadata['label'] }}
                </p>
            @endif
        </div>

        <!-- Instructions -->
        <div class="qr-instructions">
            <h3>📱 How to Use This QR Code:</h3>
            <ol>
                <li>Open the CAG Attendance mobile app or web portal</li>
                <li>Navigate to "Mark Attendance" section</li>
                <li>Select your attendance type (Office Day or Client Visit)</li>
                <li>Tap the scan button and point your camera at this QR code</li>
                <li>Wait for confirmation of successful check-in or check-out</li>
            </ol>
        </div>

        <!-- Footer -->
        <div class="qr-footer">
            Generated on {{ now()->format('F d, Y') }} |
            @if($qrCode->valid_from || $qrCode->valid_until)
                Valid
                @if($qrCode->valid_from) from {{ \Carbon\Carbon::parse($qrCode->valid_from)->format('M d, Y') }} @endif
                @if($qrCode->valid_until) until {{ \Carbon\Carbon::parse($qrCode->valid_until)->format('M d, Y') }} @endif
                |
            @endif
            Status: {{ $qrCode->is_active ? 'Active' : 'Inactive' }}
        </div>
    </div>

    <script>
        // Auto-print prompt on load (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         if (confirm('Ready to print?')) {
        //             window.print();
        //         }
        //     }, 500);
        // };
    </script>
</body>
</html>
