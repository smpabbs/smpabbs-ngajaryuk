// ===============================
//  SERVICE WORKER – IMAGE CACHE ONLY
// ===============================

const CACHE_NAME = "backup-image-cache-v1";

const PRECACHE_URLS = [
    "/offline.html"
];

// ===============================
// INSTALL
// ===============================
self.addEventListener("install", event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE_URLS))
    );
});

// ===============================
// ACTIVATE
// ===============================
self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            )
        )
    );
    self.clients.claim();
});

// ===============================
// FETCH HANDLER (IMAGE ONLY)
// ===============================
self.addEventListener("fetch", event => {

    const req = event.request;
    const url = new URL(req.url);

    // 1. Hanya HTTP/HTTPS
    if (!url.protocol.startsWith("http")) return;

    // 2. Hanya GET
    if (req.method !== "GET") return;

    // 3. Cek apakah request gambar
    const isImage =
        req.destination === "image" ||
        /\.(png|jpe?g|webp|gif|svg)$/i.test(url.pathname);

    // 4. Kalau bukan gambar → biarkan lewat
    if (!isImage) return;

    // 5. Cache First Strategy (IMAGE)
    event.respondWith(
        caches.open(CACHE_NAME).then(async cache => {
            const cached = await cache.match(req);
            if (cached) return cached;

            try {
                const fresh = await fetch(req);
                if (fresh.ok) {
                    cache.put(req, fresh.clone());
                }
                return fresh;
            } catch {
                return caches.match("/offline.html");
            }
        })
    );
});
