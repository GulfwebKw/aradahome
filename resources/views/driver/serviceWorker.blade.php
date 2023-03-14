const staticDevCoffee = "dev-coffee-site-v1"
const CACHE_NAME = 'efan-app-cache';

const assets = [
"/",
"/index.html",
"{!! ('admin_assets/assets/plugins/global/plugins.bundle.css') !!}",
"{!! ('admin_assets/assets/css/style.bundle.css') !!}",
"{!! ('admin_assets/assets/css/skins/header/base/light.css') !!}",
"{!! ('admin_assets/assets/css/skins/header/menu/light.css') !!}",
"{!! ('admin_assets/assets/css/skins/brand/light.css') !!}",
"{!! ('admin_assets/assets/css/skins/aside/dark.css') !!}",
"{!! ('uploads/logo/'.$settingInfo->favicon) !!}",
"{!! ('assets/css/kufi/kufi.eot?#iefix') !!}",
"{!! ('assets/css/kufi/kufi.woff') !!}",
"{!! ('assets/css/kufi/kufi.ttf') !!}",
"{!! ('assets/css/kufi/kufi.svg#DroidArabicKufi') !!}",
"{!! ('uploads/logo/'.$settingInfo['logo']) !!}",
"{!! ('uploads/users/no-image.png') !!}",
"{!! ('admin_assets/assets/plugins/global/plugins.bundle.js') !!}",
"{!! ('admin_assets/assets/js/scripts.bundle.js') !!}",
"{{ Auth::guard('driver')->check() ? ('uploads/users/'.Auth::guard('driver')->user()->avatar) : ('uploads/users/'.Auth::guard('admin')->user()->image) }}",
]

self.addEventListener("install", installEvent => {
installEvent.waitUntil(
caches.open(staticDevCoffee).then(cache => {
cache.addAll(assets)
})
)
})

self.addEventListener("fetch", (event) => {
if (event.request.method !== 'GET') {
return;
}
var origin = self.location.origin;
var key = event.request.url.substring(origin.length + 1);
// Redirect URLs to the index.html
if (key.indexOf('?v=') != -1) {
key = key.split('?v=')[0];
}
if (event.request.url == origin || event.request.url.startsWith(origin + '/#') || key == '') {
key = '/';
}
// If the URL is the index.html, perform an online-first request.
if (key == '/') {
return onlineFirst(event);
}
event.respondWith(caches.open(CACHE_NAME)
.then((cache) =>  {
return cache.match(event.request).then((response) => {
// Either respond with the cached resource, or perform a fetch and
// lazily populate the cache.
return response || fetch(event.request).then((response) => {
cache.put(event.request, response.clone());
return response;
});
})
})
);
});

// Attempt to download the resource online before falling back to
// the offline cache.
function onlineFirst(event) {
return event.respondWith(
fetch(event.request).then((response) => {
return caches.open(CACHE_NAME).then((cache) => {
cache.put(event.request, response.clone());
return response;
});
}).catch((error) => {
return caches.open(CACHE_NAME).then((cache) => {
return cache.match(event.request).then((response) => {
if (response != null) {
return response;
}
throw error;
});
});
})
);
}
