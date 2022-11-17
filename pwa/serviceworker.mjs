const { APPLICATION_CACHE_FILES, APPLICATION_CACHE_VERSION } = {"APPLICATION_CACHE_VERSION":"12731a59-21ef-4e97-aa6f-e3ffa600ded8","APPLICATION_CACHE_FILES":["","app.mjs","appmanifest.webmanifest","assets/illustration-04.svg","assets/pegasus.webp","behaviors/api/api.json","contexts/flux-app/src/Adapters/Api/FluxAppApi.mjs","contexts/flux-app/src/Adapters/Api/OutboundAdapter.mjs","contexts/flux-app/src/Core/Aggregate.mjs","contexts/flux-layout-component/behaviors/api/bak","contexts/flux-layout-component/behaviors/api/flux-layout-component.api.json","contexts/flux-layout-component/behaviors/css/stylesheet.css","contexts/flux-layout-component/behaviors/templates/flux-layout-content-container-template.mjs","contexts/flux-layout-component/behaviors/templates/flux-layout-header-template.mjs","contexts/flux-layout-component/behaviors/templates/flux-layout-map-template.mjs","contexts/flux-layout-component/behaviors/templates/flux-layout-menu-item-template.mjs","contexts/flux-layout-component/behaviors/templates/flux-layout-menu-template.mjs","contexts/flux-layout-component/behaviors/templates/flux-layout-menu-title-template.mjs","contexts/flux-layout-component/src/Adapters/Api/FluxLayoutComponentApi.mjs","contexts/flux-layout-component/src/Adapters/Api/InitializeFluxLayoutComponent.mjs","contexts/flux-layout-component/src/Adapters/Api/OutboundAdapter.mjs","contexts/flux-layout-component/src/Core/Aggregate.mjs","contexts/flux-layout-component/src/Core/Content.mjs","contexts/flux-layout-component/src/Core/DomainMessage.mjs","contexts/flux-layout-component/src/Core/Element.mjs","contexts/flux-layout-component/src/Core/Slot.mjs","contexts/flux-layout-component/src/Core/State.mjs","contexts/flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs","contexts/flux-repository/behaviors/api/api.json","contexts/flux-repository/src/Adapters/Api/FluxRepositoryApi.mjs","contexts/flux-repository/src/Adapters/Api/InitializeFluxRepository.mjs","contexts/flux-repository/src/Adapters/Api/OutboundAdapter.mjs","contexts/flux-repository/src/Core/Aggregate.mjs","contexts/flux-repository/src/Core/CacheStorage.mjs","contexts/flux-repository/src/Core/DomainMessage.mjs","contexts/flux-repository/src/Core/EncryptedCacheStorage.mjs","contexts/flux-repository/src/Core/ExternalHttpStorage.mjs","index.html","modules/flux-layout-component/behaviors/templates/flux-layout-container-template.json","modules/flux-layout-component/src/Adapters/Api/Initialize.mjs","serviceworker-template.mjs","serviceworker.mjs"]};

const APPLICATION_CACHE_PREFIX = "learnplaces-application-";

const APPLICATION_CACHE_NAME = `${APPLICATION_CACHE_PREFIX}${APPLICATION_CACHE_VERSION}`;

/**
 * @returns {Promise<Cache>}
 */
async function getApplicationCache() {
    return caches.open(APPLICATION_CACHE_NAME);
}

/**
 * @returns {Promise<void>}
 */
async function cacheApplicationFiles() {
    (await getApplicationCache()).addAll(APPLICATION_CACHE_FILES.map(request => `/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/${request}`));
}

/**
 * @param {Request} request
 * @param {Response} response
 * @returns {Promise<void>}
 */
async function cacheApplicationResponse(request, response) {
    (await getApplicationCache()).put(request, response);
}

/**
 * @param {Request} request
 * @returns {Promise<Response | null>}
 */
async function getApplicationCacheResponse(request) {
    return await (await getApplicationCache()).match(request) ?? null;
}

/**
 * @returns {Promise<void>}
 */
async function deleteOldApplicationCaches() {
    await Promise.all((await caches.keys()).filter(key => key.startsWith(APPLICATION_CACHE_PREFIX) && key !== APPLICATION_CACHE_NAME).map(async key => caches.delete(key)));
}

/**
 * @returns {Promise<void>}
 */
async function installEvent() {
    await cacheApplicationFiles();
}

/**
 * @param {Request} request
 * @returns {Promise<Response>}
 */
async function fetchEvent(request) {
    if (!request.url.includes("/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/") || request.url.includes(".php")) {
        return fetch(request);
    }

    const cache_response = await getApplicationCacheResponse(
        request
    );
    if (cache_response !== null) {
        return cache_response;
    }

    const response = await fetch(request);

    if (response.ok) {
        cacheApplicationResponse(
            request,
            response.clone()
        );
    }

    return response;
}

/**
 * @returns {Promise<void>}
 */
async function activateEvent() {
    await clients.claim();

    await deleteOldApplicationCaches();
}

addEventListener("install", e => {
    e.waitUntil(installEvent());
});

addEventListener("fetch", e => {
    e.respondWith(fetchEvent(
        e.request
    ));
});

addEventListener("activate", e => {
    e.waitUntil(activateEvent());
});
