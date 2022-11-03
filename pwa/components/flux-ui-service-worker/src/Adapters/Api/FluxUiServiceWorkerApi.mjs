export default class FluxUiServiceWorkerApi {

  static create() {
    return new this()
  }

  constructor() {
    this.register();
  }

  register() {
    const scriptElement = document.createElement("script");
    scriptElement.innerHTML = "if (navigator.serviceWorker.controller) {\n" +
      "      console.log(\"Active service worker found\");\n" +
      "    } else {\n" +
      "      navigator.serviceWorker\n" +
      "      .register(\"/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/serviceworker.js\", {\n" +
      "        scope: \"./\"\n" +
      "      })\n" +
      "      .then(function (reg) {\n" +
      "        console.log(\"Service worker  registered\");\n" +
      "      });\n" +
      "    }"

      document.head.insertBefore(scriptElement, document.head.childNodes[0]);
  }

  /**
   * self.addEventListener('fetch', function(event) {
   *   event.respondWith(
   *     caches.match(event.request)
   *       .then(function(response) {
   *         // Cache hit - return response
   *         if (response) {
   *           return response;
   *         }
   *         return fetch(event.request);
   *       }
   *     )
   *   );
   * });
   *
   *
   * self.addEventListener('activate', function(event) {
   *   var cacheWhitelist = ['pigment'];
   *   event.waitUntil(
   *     caches.keys().then(function(cacheNames) {
   *       return Promise.all(
   *         cacheNames.map(function(cacheName) {
   *           if (cacheWhitelist.indexOf(cacheName) === -1) {
   *             return caches.delete(cacheName);
   *           }
   *         })
   *       );
   *     })
   *   );
   * });
   */



}