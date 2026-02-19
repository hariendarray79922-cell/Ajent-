<?php
// Create main data folder if not exists
if (!file_exists('data')) {
    mkdir('data', 0777, true);
}

$submissionId = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate unique submission ID
    $submissionId = date('Ymd_His') . '_' . uniqid();
    $submissionDir = 'data/' . $submissionId;
    mkdir($submissionDir, 0777, true);

    // Save text fields to details.txt
    $textData = "=== STUDYELITE AGENT SUBMISSION ===\n";
    $textData .= "Submission ID: $submissionId\n";
    $textData .= "Submission Time: " . date('Y-m-d H:i:s') . "\n";
    $textData .= "Full Name: " . ($_POST['fullname'] ?? '') . "\n";
    $textData .= "Mobile: " . ($_POST['mobile'] ?? '') . "\n";
    $textData .= "Alternate Phone: " . ($_POST['altphone'] ?? '') . "\n";
    $textData .= "Bank Account: " . ($_POST['account'] ?? '') . "\n";
    $textData .= "IFSC Code: " . ($_POST['ifsc'] ?? '') . "\n";
    $textData .= "Account Holder: " . ($_POST['acc_holder'] ?? '') . "\n";
    $textData .= "Aadhaar Number: " . ($_POST['aadhaar_no'] ?? '') . "\n";
    file_put_contents($submissionDir . '/details.txt', $textData);

    // Function to save uploaded files
    function saveUploadedFile($fileKey, $targetDir, $filenamePrefix) {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES[$fileKey]['tmp_name'];
            $extension = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
            $targetPath = $targetDir . '/' . $filenamePrefix . '.' . $extension;
            move_uploaded_file($tmpName, $targetPath);
            return true;
        }
        return false;
    }

    // Save uploaded images
    saveUploadedFile('aadhaar_front', $submissionDir, 'aadhaar_front');
    saveUploadedFile('aadhaar_back', $submissionDir, 'aadhaar_back');
    saveUploadedFile('passport_photo', $submissionDir, 'passport_photo');

    // Save signature from canvas (data URL)
    if (!empty($_POST['signature'])) {
        $sigData = $_POST['signature'];
        // Remove data URL prefix "data:image/png;base64,"
        $sigData = str_replace('data:image/png;base64,', '', $sigData);
        $sigData = str_replace(' ', '+', $sigData);
        $sigBinary = base64_decode($sigData);
        if ($sigBinary !== false) {
            file_put_contents($submissionDir . '/signature.png', $sigBinary);
        }
    }

    $success = true;
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STUDYELITE | एजेंट पंजीकरण</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0.8;
            z-index: -1;
        }

        .circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.2);
            animation: animate 25s linear infinite;
            bottom: -150px;
            border-radius: 50%;
        }

        @keyframes animate {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; }
        }

        .circles li:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        .circles li:nth-child(2) { left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .circles li:nth-child(3) { left: 70%; width: 20px; height: 20px; animation-delay: 4s; }
        .circles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
        .circles li:nth-child(5) { left: 65%; width: 20px; height: 20px; animation-delay: 0s; }
        .circles li:nth-child(6) { left: 75%; width: 110px; height: 110px; animation-delay: 3s; }
        .circles li:nth-child(7) { left: 35%; width: 150px; height: 150px; animation-delay: 7s; }
        .circles li:nth-child(8) { left: 50%; width: 25px; height: 25px; animation-delay: 15s; animation-duration: 45s; }
        .circles li:nth-child(9) { left: 20%; width: 15px; height: 15px; animation-delay: 2s; animation-duration: 35s; }
        .circles li:nth-child(10) { left: 85%; width: 150px; height: 150px; animation-delay: 0s; animation-duration: 11s; }

        .container {
            max-width: 900px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
            z-index: 10;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .header .badge {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 8px 25px;
            border-radius: 50px;
            display: inline-block;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 10px 20px -5px rgba(247, 87, 108, 0.5);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .progress-bar::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
        }

        .progress-step {
            width: 40px;
            height: 40px;
            background: white;
            border: 2px solid #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #667eea;
            position: relative;
            z-index: 2;
            background: white;
        }

        .progress-step.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .progress-step.completed {
            background: #4caf50;
            border-color: #4caf50;
            color: white;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .full-width {
            grid-column: span 2;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
        }

        label i {
            color: #667eea;
            font-size: 1.2rem;
            width: 24px;
        }

        input, textarea, select {
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 1rem;
            background: white;
            transition: all 0.3s;
            outline: none;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .file-upload {
            position: relative;
            border: 2px dashed #667eea;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload:hover {
            background: #e8ebff;
            border-color: #764ba2;
        }

        .file-upload input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload i {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 10px;
        }

        .file-upload p {
            color: #666;
            font-size: 0.9rem;
        }

        .signature-area {
            background: #f8f9ff;
            border-radius: 15px;
            padding: 20px;
        }

        .signature-canvas {
            width: 100%;
            height: 150px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background: white;
            cursor: crosshair;
        }

        .signature-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
        }

        .btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.5);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            font-size: 1.2rem;
            width: 100%;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(102, 126, 234, 0.6);
        }

        .info-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .info-card i {
            font-size: 2.5rem;
        }

        .info-card p {
            font-size: 1.1rem;
            line-height: 1.5;
        }

        .success-box {
            background: linear-gradient(135deg, #a8e6cf 0%, #d4edda 100%);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: #155724;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .success-box i {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        .success-box h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .success-box p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .success-box .btn {
            background: #28a745;
            color: white;
            border: none;
        }

        .success-box .btn:hover {
            background: #218838;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: span 1;
            }

            .header h1 {
                font-size: 2rem;
            }

            .progress-bar {
                display: none;
            }
        }

        /* Loading animation */
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .loading.active {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading p {
            margin-top: 15px;
            font-size: 1.2rem;
            color: #667eea;
            font-weight: 600;
        }

        /* Tooltip */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background: #333;
            color: white;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>
    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <div class="loading" id="loading">
        <div class="spinner"></div>
        <p>आपका डेटा सेव हो रहा है...</p>
    </div>

    <div class="container">
        <div class="header">
            <h1>📚 STUDYELITE</h1>
            <div class="badge">
                <i class="fas fa-star"></i> एजेंट पंजीकरण 2026
            </div>
        </div>

        <div class="progress-bar">
            <div class="progress-step active">1</div>
            <div class="progress-step">2</div>
            <div class="progress-step">3</div>
            <div class="progress-step">4</div>
        </div>

        <?php if ($success): ?>
        <div class="success-box">
            <i class="fas fa-check-circle"></i>
            <h2>🎉 बधाई हो! आपका पंजीकरण सफल हुआ</h2>
            <p><strong>सबमिशन आईडी:</strong> <?php echo htmlspecialchars($submissionId); ?></p>
            <p>आपके सभी दस्तावेज सुरक्षित सेव हो गए हैं।</p>
            <p>जल्द ही STUDYELITE टीम आपसे संपर्क करेगी।</p>
            <button class="btn" onclick="window.location.href=''">
                <i class="fas fa-redo"></i> नया पंजीकरण करें
            </button>
        </div>
        <?php else: ?>

        <div class="info-card">
            <i class="fas fa-info-circle"></i>
            <p>💰 ₹8000 प्रति माह वेतन | ✅ 12वीं पास | 📱 वैध आधार व बैंक खाता अनिवार्य</p>
        </div>

        <form method="post" enctype="multipart/form-data" id="agentForm" onsubmit="showLoading()">
            <div class="form-grid">
                <!-- Personal Information -->
                <div class="field full-width">
                    <label><i class="fas fa-user-circle"></i> पूरा नाम (जैसा आधार पर है) <span style="color:red;">*</span></label>
                    <input type="text" name="fullname" placeholder="उदा: राजेश कुमार" required>
                </div>

                <div class="field">
                    <label><i class="fas fa-mobile-alt"></i> मोबाइल नंबर <span style="color:red;">*</span></label>
                    <input type="tel" name="mobile" placeholder="9876543210" pattern="[0-9]{10}" required>
                </div>

                <div class="field">
                    <label><i class="fas fa-phone-alt"></i> वैकल्पिक फोन</label>
                    <input type="tel" name="altphone" placeholder="दूसरा नंबर">
                </div>

                <!-- Bank Details -->
                <div class="field full-width">
                    <label><i class="fas fa-university"></i> बैंक खाता संख्या <span style="color:red;">*</span></label>
                    <input type="text" name="account" placeholder="उदा: 12345678901" required>
                </div>

                <div class="field">
                    <label><i class="fas fa-building"></i> IFSC कोड <span style="color:red;">*</span></label>
                    <input type="text" name="ifsc" placeholder="उदा: SBIN0001234" required>
                </div>

                <div class="field">
                    <label><i class="fas fa-user"></i> खाता धारक का नाम <span style="color:red;">*</span></label>
                    <input type="text" name="acc_holder" placeholder="जैसा पासबुक में" required>
                </div>

                <!-- Aadhaar Details -->
                <div class="field full-width">
                    <label><i class="fas fa-id-card"></i> आधार कार्ड नंबर <span style="color:red;">*</span></label>
                    <input type="text" name="aadhaar_no" placeholder="1234 5678 9012" pattern="[0-9]{12}" required>
                </div>

                <div class="field">
                    <label><i class="fas fa-image"></i> आधार फ्रंट फोटो <span style="color:red;">*</span></label>
                    <div class="file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>क्लिक करें या फाइल ड्रैग करें</p>
                        <input type="file" name="aadhaar_front" accept="image/*" required>
                    </div>
                </div>

                <div class="field">
                    <label><i class="fas fa-image"></i> आधार बैक फोटो <span style="color:red;">*</span></label>
                    <div class="file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>क्लिक करें या फाइल ड्रैग करें</p>
                        <input type="file" name="aadhaar_back" accept="image/*" required>
                    </div>
                </div>

                <div class="field full-width">
                    <label><i class="fas fa-camera"></i> पासपोर्ट साइज फोटो <span style="color:red;">*</span></label>
                    <div class="file-upload">
                        <i class="fas fa-portrait"></i>
                        <p>अपनी फोटो अपलोड करें (सेल्फी भी चलेगी)</p>
                        <input type="file" name="passport_photo" accept="image/*" required>
                    </div>
                </div>

                <!-- Signature -->
                <div class="field full-width signature-area">
                    <label><i class="fas fa-pen-fancy"></i> डिजिटल हस्ताक्षर <span style="color:red;">*</span></label>
                    <canvas id="signatureCanvas" class="signature-canvas" width="600" height="150"></canvas>
                    <div class="signature-actions">
                        <button type="button" class="btn" id="clearSignature">
                            <i class="fas fa-eraser"></i> साफ करें
                        </button>
                        <button type="button" class="btn" id="saveSignature">
                            <i class="fas fa-save"></i> सहेजें
                        </button>
                        <span class="tooltip" style="margin-left: auto;">
                            <i id="sigStatus" class="fas fa-times-circle" style="color:red;"></i>
                            <span class="tooltiptext">हस्ताक्षर सहेजें बटन दबाएं</span>
                        </span>
                    </div>
                    <input type="hidden" id="signatureData" name="signature">
                </div>

                <!-- Terms -->
                <div class="field full-width">
                    <label style="flex-direction: row; gap: 10px;">
                        <input type="checkbox" required style="width: 20px; height: 20px;">
                        <span>मैं पुष्टि करता हूँ कि मेरी आयु 18+ है, 12वीं पास हूँ, और सभी दिए गए दस्तावेज सही हैं।</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane"></i> आवेदन जमा करें
            </button>
        </form>
        <?php endif; ?>
    </div>

    <script>
        function showLoading() {
            if (!document.getElementById('signatureData').value) {
                event.preventDefault();
                alert('कृपया पहले हस्ताक्षर बनाएँ और "सहेजें" बटन दबाएँ।');
                return false;
            }
            document.getElementById('loading').classList.add('active');
        }

        (function() {
            const canvas = document.getElementById('signatureCanvas');
            const ctx = canvas.getContext('2d');
            let drawing = false;
            
            ctx.strokeStyle = '#667eea';
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';

            function getMousePos(e) {
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;
                let clientX, clientY;
                
                if (e.touches) {
                    clientX = e.touches[0].clientX;
                    clientY = e.touches[0].clientY;
                } else {
                    clientX = e.clientX;
                    clientY = e.clientY;
                }
                
                const x = (clientX - rect.left) * scaleX;
                const y = (clientY - rect.top) * scaleY;
                return { 
                    x: Math.min(canvas.width - 1, Math.max(0, x)), 
                    y: Math.min(canvas.height - 1, Math.max(0, y)) 
                };
            }

            function startDrawing(e) {
                e.preventDefault();
                drawing = true;
                const pos = getMousePos(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }

            function draw(e) {
                if (!drawing) return;
                e.preventDefault();
                const pos = getMousePos(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }

            function stopDrawing() {
                drawing = false;
                ctx.beginPath();
            }

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseleave', stopDrawing);
            canvas.addEventListener('touchstart', startDrawing, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stopDrawing);
            canvas.addEventListener('touchcancel', stopDrawing);

            document.getElementById('clearSignature').addEventListener('click', function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.beginPath();
                document.getElementById('signatureData').value = '';
                document.getElementById('sigStatus').className = 'fas fa-times-circle';
                document.getElementById('sigStatus').style.color = 'red';
            });

            document.getElementById('saveSignature').addEventListener('click', function() {
                const dataURL = canvas.toDataURL('image/png');
                document.getElementById('signatureData').value = dataURL;
                document.getElementById('sigStatus').className = 'fas fa-check-circle';
                document.getElementById('sigStatus').style.color = 'green';
                
                // Show success message
                const tooltip = document.querySelector('.tooltip .tooltiptext');
                tooltip.textContent = 'हस्ताक्षर सहेजा गया';
                tooltip.style.background = '#28a745';
                
                setTimeout(() => {
                    tooltip.textContent = 'हस्ताक्षर सहेजें बटन दबाएं';
                    tooltip.style.background = '#333';
                }, 2000);
            });
        })();
    </script>
</body>
</html>
