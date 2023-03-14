const staticDevCoffee = "dev-coffee-site-v1"
const assets = [
    "/",
    "/index.html",
    "{!! url('admin_assets/assets/plugins/global/plugins.bundle.css') !!}",
    "{!! url('admin_assets/assets/css/style.bundle.css') !!}",
    "{!! url('admin_assets/assets/css/skins/header/base/light.css') !!}",
    "{!! url('admin_assets/assets/css/skins/header/menu/light.css') !!}",
    "{!! url('admin_assets/assets/css/skins/brand/light.css') !!}",
    "{!! url('admin_assets/assets/css/skins/aside/dark.css') !!}",
    "{!! url('uploads/logo/'.$settingInfo->favicon) !!}",
    "{!! url('assets/css/kufi/kufi.eot?#iefix') !!}",
    "{!! url('assets/css/kufi/kufi.woff') !!}",
    "{!! url('assets/css/kufi/kufi.ttf') !!}",
    "{!! url('assets/css/kufi/kufi.svg#DroidArabicKufi') !!}",
    "{!! url('uploads/logo/'.$settingInfo['logo']) !!}",
    "{!! url('uploads/users/no-image.png') !!}",
    "{!! url('admin_assets/assets/plugins/global/plugins.bundle.js') !!}",
    "{!! url('admin_assets/assets/js/scripts.bundle.js') !!}",
    "{{ Auth::guard('driver')->check() ? url('uploads/users/'.Auth::guard('driver')->user()->avatar) : url('uploads/users/'.Auth::guard('admin')->user()->image) }}",
]

self.addEventListener("install", installEvent => {
    installEvent.waitUntil(
        caches.open(staticDevCoffee).then(cache => {
            cache.addAll(assets)
        })
    )
})

// self.addEventListener("fetch", fetchEvent => {
//     fetchEvent.respondWith(
//         caches.match(fetchEvent.request).then(res => {
//             return res || fetch(fetchEvent.request)
//         })
//     )
// })
