<div x-data="{
    show: false,
    title: '',
    message: '',
    confirmText: 'Ya',
    cancelText: 'Batal',
    onConfirm: null,
    onCancel: null
}" 
x-show="show" 
x-transition:enter="ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3">
    <div x-on:click.outside="show = false; onCancel && onCancel()" 
         class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden">
        <div class="px-5 py-4">
            <div class="font-semibold" x-text="title"></div>
            <p class="text-slate-600 mt-1" x-text="message"></p>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
            <button x-on:click="show = false; onCancel && onCancel()" 
                    class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" 
                    x-text="cancelText"></button>
            <button x-on:click="show = false; onConfirm && onConfirm()" 
                    class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700" 
                    x-text="confirmText"></button>
        </div>
    </div>
</div>

<script>
    // Modal confirm global
document.addEventListener('alpine:init', () => {
    Alpine.data('confirmModal', () => ({
        show: false,
        title: '',
        message: '',
        confirmText: 'Ya',
        cancelText: 'Batal',
        onConfirm: null,
        onCancel: null,
        
        open(options) {
            this.title = options.title || 'Konfirmasi';
            this.message = options.message || 'Apakah Anda yakin?';
            this.confirmText = options.confirmText || 'Ya';
            this.cancelText = options.cancelText || 'Batal';
            this.onConfirm = options.onConfirm || null;
            this.onCancel = options.onCancel || null;
            this.show = true;
        },
        
        close() {
            this.show = false;
        },
        
        confirm() {
            if (this.onConfirm) this.onConfirm();
            this.close();
        },
        
        cancel() {
            if (this.onCancel) this.onCancel();
            this.close();
        }
    }));
});

// Usage in invoicePenjualan:
// this.$dispatch('confirm-modal', {
//     title: 'Hapus Invoice',
//     message: 'Apakah Anda yakin ingin menghapus invoice ini?',
//     onConfirm: () => this.deleteInvoice(id)
// });
</script>
