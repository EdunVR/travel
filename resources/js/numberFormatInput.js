class NumberFormatInput {
    static init() {
        document.querySelectorAll('.number-format-input').forEach(input => {
            // Format saat kehilangan fokus
            input.addEventListener('blur', function() {
                this.value = this.value ? Number(this.value.replace(/[^0-9]/g, '')).toLocaleString('id-ID') : '';
            });

            // Hapus format saat mendapatkan fokus
            input.addEventListener('focus', function() {
                this.value = this.value ? this.value.replace(/[^0-9]/g, '') : '';
            });

            // Validasi input
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    }
}

export default NumberFormatInput;