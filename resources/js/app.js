import "./bootstrap";
import "trix";

const pwaInstallController = (() => {
    let deferredPrompt = null;
    let banner = null;
    let installButton = null;
    let dismissButton = null;
    const dismissalKey = "fdf.pwa.install.dismissed";

    const isStandalone = () =>
        window.matchMedia("(display-mode: standalone)").matches ||
        window.navigator.standalone === true;

    const setVisible = (visible) => {
        if (!banner) {
            return;
        }

        banner.setAttribute("data-visible", visible ? "true" : "false");
        banner.setAttribute("aria-hidden", visible ? "false" : "true");
    };

    const canShow = () =>
        !isStandalone() && !window.localStorage.getItem(dismissalKey);

    window.addEventListener("beforeinstallprompt", (event) => {
        event.preventDefault();
        deferredPrompt = event;

        if (canShow()) {
            setVisible(true);
        }
    });

    window.addEventListener("appinstalled", () => {
        deferredPrompt = null;
        window.localStorage.removeItem(dismissalKey);
        setVisible(false);
    });

    if ("serviceWorker" in navigator) {
        window.addEventListener("load", () => {
            navigator.serviceWorker.register("/sw.js").catch(() => undefined);
        });
    }

    return {
        bind(bannerNode, installNode, dismissNode) {
            banner = bannerNode;
            installButton = installNode;
            dismissButton = dismissNode;

            if (installButton && !installButton.dataset.bound) {
                installButton.dataset.bound = "true";
                installButton.addEventListener("click", async () => {
                    if (!deferredPrompt) {
                        return;
                    }

                    deferredPrompt.prompt();
                    const choice = await deferredPrompt.userChoice;

                    if (choice.outcome !== "accepted") {
                        window.localStorage.setItem(dismissalKey, "1");
                    }

                    deferredPrompt = null;
                    setVisible(false);
                });
            }

            if (dismissButton && !dismissButton.dataset.bound) {
                dismissButton.dataset.bound = "true";
                dismissButton.addEventListener("click", () => {
                    window.localStorage.setItem(dismissalKey, "1");
                    setVisible(false);
                });
            }

            if (!canShow()) {
                setVisible(false);

                return;
            }

            if (deferredPrompt) {
                setVisible(true);
            }
        },
    };
})();

window.pwaInstallBannerController = pwaInstallController;

window.richTextEditor = function (contentState) {
    return {
        contentState,
        syncing: false,
        init() {
            const input = this.$refs.input;
            const editor = this.$refs.editor;

            const applyContent = (value = "") => {
                const normalized = value ?? "";

                if ((input.value ?? "") === normalized) {
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

            editor.addEventListener("trix-file-accept", (event) => {
                event.preventDefault();
            });

            editor.addEventListener("trix-change", () => {
                if (this.syncing) {
                    return;
                }

                this.contentState = input.value;
            });

            this.$watch("contentState", (value) => {
                if (this.syncing) {
                    return;
                }

                applyContent(value);
            });
        },
    };
};
