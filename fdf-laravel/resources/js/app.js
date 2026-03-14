import './bootstrap';
import 'trix';

window.richTextEditor = function (contentState) {
    return {
        contentState,
        syncing: false,
        init() {
            const input = this.$refs.input;
            const editor = this.$refs.editor;

            const applyContent = (value = '') => {
                const normalized = value ?? '';

                if ((input.value ?? '') === normalized) {
                    return;
                }

                this.syncing = true;
                input.value = normalized;

                if (editor.editor) {
                    editor.editor.loadHTML(normalized);
                }

                this.$nextTick(() => {
                    this.syncing = false;
                });
            };

            applyContent(this.contentState);

            editor.addEventListener('trix-file-accept', (event) => {
                event.preventDefault();
            });

            editor.addEventListener('trix-change', () => {
                if (this.syncing) {
                    return;
                }

                this.contentState = input.value;
            });

            this.$watch('contentState', (value) => {
                if (this.syncing) {
                    return;
                }

                applyContent(value);
            });
        },
    };
};
