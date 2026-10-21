{{--
    Componente de Escáner QR Reutilizable
    Uso: @include('components.qr-scanner', ['route' => '/prueba3', 'successMessage' => 'QR escaneado correctamente'])
--}}

<div class="qr-scanner-container">
    <div class="qr-scanner-header">
        <h3>📱 Escanear Código QR</h3>
        <p>Apunta la cámara hacia el código QR</p>
    </div>

    <div id="reader" class="qr-reader"></div>

    <div id="qr-result" class="qr-result" style="display: none;">
        <div class="qr-success">
            <i class="ph-fill ph-check-circle"></i>
            <span id="qr-message">QR detectado</span>
        </div>
    </div>

    <div id="qr-error" class="qr-error" style="display: none;">
        <i class="ph-fill ph-warning-circle"></i>
        <span id="qr-error-message">Error al escanear</span>
    </div>

    <button id="toggle-scanner" class="btn-scanner" onclick="toggleScanner()">
        <i class="ph ph-camera"></i> Activar Escáner
    </button>
</div>

<style>
.qr-scanner-container {
    width: 100%;
    max-width: 500px;
    margin: 2rem auto;
    padding: 1.5rem;
    background: rgba(4, 26, 14, 0.8);
    border: 1px solid var(--border, #00ff8840);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 255, 136, 0.1);
}

.qr-scanner-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.qr-scanner-header h3 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    color: var(--green, #00ff88);
    margin-bottom: 0.5rem;
    text-shadow: 0 0 10px var(--green, #00ff88);
}

.qr-scanner-header p {
    font-size: 0.9rem;
    color: var(--text, #c8ffdf);
    opacity: 0.8;
}

.qr-reader {
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    background: #000;
    display: none;
    margin-bottom: 1rem;
}

.qr-reader.active {
    display: block;
}

#reader video {
    width: 100% !important;
    height: auto !important;
    border-radius: 8px;
}

#reader__scan_region {
    border-radius: 8px !important;
}

.qr-result, .qr-error {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 1rem;
    animation: slideIn 0.3s ease;
}

.qr-result {
    background: rgba(0, 255, 136, 0.1);
    border: 1px solid var(--green, #00ff88);
    color: var(--green, #00ff88);
}

.qr-error {
    background: rgba(255, 34, 68, 0.1);
    border: 1px solid var(--red, #ff2244);
    color: var(--red, #ff2244);
}

.qr-result i, .qr-error i {
    font-size: 1.5rem;
}

.qr-success {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.btn-scanner {
    width: 100%;
    padding: 1rem;
    font-family: 'Orbitron', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    border: 2px solid var(--green, #00ff88);
    background: rgba(0, 255, 136, 0.1);
    color: var(--green, #00ff88);
    border-radius: 8px;
    cursor: pointer;
    text-transform: uppercase;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-scanner:hover {
    background: rgba(0, 255, 136, 0.2);
    box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
}

.btn-scanner:active {
    transform: scale(0.98);
}

.btn-scanner.active {
    background: var(--red, #ff2244);
    border-color: var(--red, #ff2244);
    color: #fff;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 600px) {
    .qr-scanner-container {
        padding: 1rem;
        margin: 1rem auto;
    }

    .qr-scanner-header h3 {
        font-size: 1.2rem;
    }
}
</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let isScanning = false;
const targetRoute = "{{ $route ?? '/qr' }}";
const successMessage = "{{ $successMessage ?? 'QR escaneado correctamente' }}";

function toggleScanner() {
    const readerDiv = document.getElementById('reader');
    const toggleBtn = document.getElementById('toggle-scanner');
    const resultDiv = document.getElementById('qr-result');
    const errorDiv = document.getElementById('qr-error');

    if (!isScanning) {
        // Activar escáner
        readerDiv.classList.add('active');
        toggleBtn.classList.add('active');
        toggleBtn.innerHTML = '<i class="ph ph-x"></i> Detener Escáner';
        resultDiv.style.display = 'none';
        errorDiv.style.display = 'none';

        startScanner();
    } else {
        // Detener escáner
        stopScanner();
        readerDiv.classList.remove('active');
        toggleBtn.classList.remove('active');
        toggleBtn.innerHTML = '<i class="ph ph-camera"></i> Activar Escáner';
    }
}

function startScanner() {
    html5QrCode = new Html5Qrcode("reader");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    html5QrCode.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanError
    ).then(() => {
        isScanning = true;
    }).catch(err => {
        console.error("Error al iniciar escáner:", err);
        showError("No se pudo acceder a la cámara. Verifica los permisos.");
        document.getElementById('reader').classList.remove('active');
        document.getElementById('toggle-scanner').classList.remove('active');
        document.getElementById('toggle-scanner').innerHTML = '<i class="ph ph-camera"></i> Activar Escáner';
    });
}

function stopScanner() {
    if (html5QrCode && isScanning) {
        html5QrCode.stop().then(() => {
            isScanning = false;
            html5QrCode.clear();
        }).catch(err => {
            console.error("Error al detener escáner:", err);
        });
    }
}

function onScanSuccess(decodedText, decodedResult) {
    // Detener el escáner inmediatamente
    stopScanner();

    // Mostrar mensaje de éxito
    document.getElementById('qr-message').textContent = successMessage;
    document.getElementById('qr-result').style.display = 'flex';
    document.getElementById('reader').classList.remove('active');
    document.getElementById('toggle-scanner').style.display = 'none';

    // Vibración háptica si está disponible
    if (navigator.vibrate) {
        navigator.vibrate([100, 50, 100]);
    }

    // Enviar el código QR al servidor
    sendQRToServer(decodedText);
}

function onScanError(errorMessage) {
    // Ignorar errores de escaneo continuo (son normales)
    // Solo mostrar errores críticos
}

function sendQRToServer(qrData) {
    // Mostrar indicador de carga
    document.getElementById('qr-message').textContent = 'Procesando...';

    fetch(targetRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ qr_data: qrData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('qr-message').textContent = data.message || successMessage;

            // Redirigir si se proporciona una URL
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        } else {
            showError(data.message || 'QR inválido o no reconocido');
            // Permitir escanear de nuevo
            setTimeout(() => {
                document.getElementById('toggle-scanner').style.display = 'block';
                document.getElementById('qr-result').style.display = 'none';
                document.getElementById('qr-error').style.display = 'none';
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error de conexión con el servidor');
        setTimeout(() => {
            document.getElementById('toggle-scanner').style.display = 'block';
            document.getElementById('qr-result').style.display = 'none';
            document.getElementById('qr-error').style.display = 'none';
        }, 2000);
    });
}

function showError(message) {
    document.getElementById('qr-error-message').textContent = message;
    document.getElementById('qr-error').style.display = 'flex';
    document.getElementById('qr-result').style.display = 'none';

    if (navigator.vibrate) {
        navigator.vibrate(200);
    }
}

// Limpiar al salir de la página
window.addEventListener('beforeunload', () => {
    if (isScanning) {
        stopScanner();
    }
});
</script>
