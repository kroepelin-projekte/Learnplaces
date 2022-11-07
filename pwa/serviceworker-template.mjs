const { APPLICATION_CACHE_FILES, APPLICATION_CACHE_VERSION } = { /*%DATA%*/ };

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
