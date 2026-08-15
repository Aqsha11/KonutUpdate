import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

class OpiniUploadAdapter {
    constructor(loader, uploadUrl) {
        this.loader = loader;
        this.uploadUrl = uploadUrl;
    }
    upload() {
        return this.loader.file.then(file => new Promise((resolve, reject) => {
            const data = new FormData();
            data.append('upload', file);
            fetch(this.uploadUrl, {
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

function OpiniUploadAdapterPlugin(editor, uploadUrl) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        return new OpiniUploadAdapter(loader, uploadUrl);
    };
}

window.initOpiniEditor = function(elementId, placeholder = 'Tulis opini Anda di sini...', uploadUrl = '') {
    const element = document.getElementById(elementId);
    if (!element) return;

    ClassicEditor
        .create(element, {
            extraPlugins: uploadUrl ? [function(editor) { return OpiniUploadAdapterPlugin(editor, uploadUrl); }] : [],
            toolbar: {
                items: [
                    'undo', 'redo',
                    '|', 'heading',
                    '|', 'bold', 'italic', 'underline',
                    '|', 'alignment',
                    '|', 'bulletedList', 'numberedList',
                    '|', 'blockQuote', 'link',
                    '|', 'insertTable',
                    '|', 'imageUpload',
                    '|', 'removeFormat'
                ]
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
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
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
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
            placeholder: placeholder,
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
