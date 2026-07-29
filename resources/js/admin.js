import * as bootstrap from 'bootstrap';
import $ from 'jquery';
import 'datatables.net-bs5';

import '@fortawesome/fontawesome-free/css/fontawesome.css';
import '@fortawesome/fontawesome-free/css/brands.css';

window.$ = window.jQuery = $;

// ===== CKEditor =====
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

class AdminUploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }
    upload() {
        return this.loader.file.then(file => new Promise((resolve, reject) => {
            const data = new FormData();
            data.append('upload', file);
            fetch('/admin/posts/upload-image', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                body: data,
            })
            .then(res => res.json())
            .then(res => { resolve({ default: res.url }); })
            .catch(() => { reject('Gagal upload gambar'); });
        }));
    }
    abort() { }
}

function AdminUploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        return new AdminUploadAdapter(loader);
    };
}

window.initCKEditor = function(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;

    ClassicEditor
        .create(element, {
            extraPlugins: [AdminUploadAdapterPlugin],
            toolbar: {
                items: [
                    'undo', 'redo',
                    '|', 'heading',
                    '|', 'bold', 'italic', 'underline',
                    '|', 'alignment',
                    '|', 'bulletedList', 'numberedList',
                    '|', 'blockQuote', 'link',
                    '|', 'insertTable', 'mediaEmbed',
                    '|', 'imageUpload',
                    '|', 'removeFormat'
                ]
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                ]
            },
            image: {
                toolbar: ['imageTextAlternative', 'imageStyle:inline', 'imageStyle:block', 'imageStyle:side'],
                styles: ['full', 'side', 'alignLeft', 'alignCenter', 'alignRight']
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
            },
            link: {
                addTargetToExternalLinks: true,
                decorators: {
                    openInNewTab: {
                        mode: 'manual',
                        label: 'Buka di tab baru',
                        attributes: { target: '_blank', rel: 'noopener noreferrer' }
                    }
                }
            },
            placeholder: 'Tulis konten berita di sini...',
            shouldNotGroupWhenFull: false,
        })
        .then(editor => {
            window.editorInstance = editor;
            editor.model.document.on('change:data', () => {
                document.getElementById(elementId).value = editor.getData();
            });

            const form = element.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    document.getElementById(elementId).value = editor.getData();
                });
            }
        })
        .catch(error => {
            console.error('CKEditor error:', error);
        });
};

window.togglePreview = function() {
    const preview = document.getElementById('preview');
    if (!preview) return;
    if (preview.classList.contains('d-none')) {
        const data = window.editorInstance ? window.editorInstance.getData() : document.getElementById('editor')?.value || '';
        preview.innerHTML = '<div class="article-content">' + data + '</div>';
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
    }
};


window.showToast = function(message, type = 'success', duration = 4000) {
    const container = document.getElementById('toastAdminContainer');
    if (!container) return;

    const icons = { success: '✓', error: '✕', warning: '!', info: 'i' };
    const titles = { success: 'Berhasil', error: 'Gagal', warning: 'Peringatan', info: 'Informasi' };

    const toast = document.createElement('div');
    toast.className = `toast-admin toast-admin-${type}`;
    toast.innerHTML = `
        <div class="toast-admin-icon">${icons[type] || 'i'}</div>
        <div class="toast-admin-body">
            <div class="toast-admin-title">${titles[type] || 'Info'}</div>
            <div class="toast-admin-message">${message}</div>
        </div>
        <button class="toast-admin-close" onclick="this.closest('.toast-admin').remove()">
            <i class="bi bi-x"></i>
        </button>
    `;

    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));

    if (duration > 0) {
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 350);
        }, duration);
    }
};

// Confirm delete via modal (triggered by button click)
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-action-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            if (!form) return;

            const modalEl = document.getElementById('confirmModal');
            if (!modalEl) { form.submit(); return; }

            const modal = new bootstrap.Modal(modalEl);
            const confirmBtn = document.getElementById('confirmBtn');
            const cancelBtn = document.getElementById('confirmCancelBtn');

            const titleEl = document.getElementById('confirmTitle');
            const messageEl = document.getElementById('confirmMessage');
            const confirmIcon = document.getElementById('confirmIcon');

            if (titleEl) titleEl.textContent = 'Konfirmasi Hapus';
            if (messageEl) messageEl.innerHTML = 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.';
            if (confirmBtn) {
                confirmBtn.textContent = 'Ya, Hapus';
                confirmBtn.className = 'btn-admin btn-admin-danger';
            }
            if (confirmIcon) {
                confirmIcon.className = 'confirm-icon danger';
                confirmIcon.innerHTML = '<i class="bi bi-exclamation-triangle"></i>';
            }

            function cleanup() {
                confirmBtn?.removeEventListener('click', onConfirm);
                cancelBtn?.removeEventListener('click', onCancel);
                modalEl?.removeEventListener('hidden.bs.modal', onCancel);
            }

            function onConfirm() {
                cleanup();
                modal.hide();
                form.submit();
            }

            function onCancel() {
                cleanup();
                modal.hide();
            }

            confirmBtn?.addEventListener('click', onConfirm);
            cancelBtn?.addEventListener('click', onCancel);
            modalEl?.addEventListener('hidden.bs.modal', onCancel);

            modal.show();
        });
    });
});

// ===== Drag & Drop Upload =====
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.dropzone-admin').forEach(zone => {
        const fileInput = zone.querySelector('input[type="file"]') || zone.previousElementSibling?.querySelector('input[type="file"]');
        const preview = zone.parentElement?.querySelector('.dropzone-preview-container');

        if (!fileInput) {
            zone.addEventListener('click', () => {
                const input = zone.querySelector('input[type="file"]') ||
                    zone.parentElement.querySelector('input[type="file"]');
                if (input) input.click();
            });
        }

        ['dragenter', 'dragover'].forEach(evt => {
            zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.add('dragover'); });
        });
        ['dragleave', 'drop'].forEach(evt => {
            zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.remove('dragover'); });
        });
        zone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length && fileInput) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    });
});

// ===== Auto-dismiss Alerts =====
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.querySelectorAll('.alert-admin').forEach(el => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.style.display = 'none', 400);
        });
    }, 5000);
});
