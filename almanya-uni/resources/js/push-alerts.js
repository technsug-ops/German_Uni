// Şehir etkinlik bildirimi — tarayıcı web push aboneliği.
// [data-push-alert] butonu: izin iste → PushManager.subscribe → backend'e kaydet.

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    const out = new Uint8Array(raw.length);
    for (let i = 0; i < raw.length; i++) out[i] = raw.charCodeAt(i);
    return out;
}

function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function vapidKey() {
    return document.querySelector('meta[name="vapid-public-key"]')?.content || '';
}

function supported() {
    return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
}

async function subscribe(btn) {
    const cityId = btn.getAttribute('data-city-id');
    const key = vapidKey();
    if (!key || !cityId) return setState(btn, 'error', btn.dataset.msgError);

    setState(btn, 'loading');

    const perm = await Notification.requestPermission();
    if (perm !== 'granted') return setState(btn, 'idle', btn.dataset.msgDenied);

    try {
        const reg = await navigator.serviceWorker.ready;
        let sub = await reg.pushManager.getSubscription();
        if (!sub) {
            sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(key),
            });
        }
        const json = sub.toJSON();
        const res = await fetch(btn.dataset.subscribeUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), Accept: 'application/json' },
            body: JSON.stringify({ endpoint: sub.endpoint, keys: json.keys, city_id: Number(cityId) }),
        });
        if (!res.ok) throw new Error('subscribe failed');
        setState(btn, 'done', btn.dataset.msgDone);
    } catch (e) {
        setState(btn, 'error', btn.dataset.msgError);
    }
}

function setState(btn, state, msg) {
    btn.dataset.state = state;
    btn.disabled = state === 'loading' || state === 'done';
    const label = btn.querySelector('[data-push-label]') || btn;
    if (state === 'loading') label.textContent = btn.dataset.msgLoading || '…';
    else if (msg) label.textContent = msg;
    const note = document.querySelector(`[data-push-note="${btn.getAttribute('data-city-id')}"]`);
    if (note && msg && state !== 'loading') note.textContent = msg;
}

function init() {
    const buttons = document.querySelectorAll('[data-push-alert]');
    if (!buttons.length) return;
    if (!supported()) {
        buttons.forEach((b) => { b.style.display = 'none'; });
        return;
    }
    buttons.forEach((btn) => {
        if (Notification.permission === 'denied') {
            setState(btn, 'idle', btn.dataset.msgDenied);
        }
        btn.addEventListener('click', (e) => { e.preventDefault(); subscribe(btn); });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
