<!-- Dialog Box -->
<div id="featureUnavailableDialog" class="dialog-overlay">
    <div class="dialog-box">
        <div class="dialog-header">
            <h3>Fitur Belum Tersedia</h3>
            <button id="closeDialogBtn">&times;</button>
        </div>
        <div class="dialog-body">
            <p>Maaf, fitur ini belum tersedia. Untuk mengakses fitur ini, silakan hubungi developer.</p>
        </div>
        <div class="dialog-footer">
            <button id="contactDeveloperBtn">Hubungi Developer</button>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
    .dialog-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .dialog-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        animation: slideIn 0.3s ease-out;
    }

    .dialog-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .dialog-header h3 {
        margin: 0;
    }

    .dialog-header button {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
    }

    .dialog-body {
        margin-bottom: 20px;
    }

    .dialog-footer {
        text-align: right;
    }

    .dialog-footer button {
        padding: 8px 16px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .dialog-footer button:hover {
        background: #0056b3;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<!-- JavaScript -->
<script>
    // Pindahkan fungsi ke scope global
    function showUnavailableFeatureDialog() {
        const dialog = document.getElementById('featureUnavailableDialog');
        dialog.style.display = 'flex';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const dialog = document.getElementById('featureUnavailableDialog');
        const closeDialogBtn = document.getElementById('closeDialogBtn');
        const contactDeveloperBtn = document.getElementById('contactDeveloperBtn');

        // Tutup dialog saat tombol close diklik
        closeDialogBtn.addEventListener('click', function () {
            dialog.style.display = 'none';
        });

        // Aksi saat tombol "Hubungi Developer" diklik
        contactDeveloperBtn.addEventListener('click', function () {
            alert('Silakan hubungi developer melalui email: developer@example.com');
            dialog.style.display = 'none';
        });

        // Tutup dialog saat klik di luar dialog
        window.addEventListener('click', function (e) {
            if (e.target === dialog) {
                dialog.style.display = 'none';
            }
        });
    });
</script>
