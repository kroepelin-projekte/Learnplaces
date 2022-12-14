import FluxGatewayApi from './libs/gateway/src/Adapters/Api/FluxGatewayApi.mjs';
import FluxRepositoryApi from './libs/repository/src/Adapters/Api/FluxRepositoryApi.mjs';

import FluxLayoutApi from "./libs/layout/src/Adapters/Api/FluxLayoutApi.mjs";
import FluxLayoutConfig from "./libs/layout/src/Adapters/Api/FluxLayoutConfig.mjs";



const __dirname = import.meta.url.substring(0, import.meta.url.lastIndexOf("/"));

const fetch_api = (await import("./libs/flux-fetch-api/src/Adapter/Api/FetchApi.mjs")).FetchApi.new();

const css_api = (await import("./libs/flux-css-api/src/Adapter/Api/CssApi.mjs")).CssApi.new(
    fetch_api
);
await css_api.init();

const json_api = (await import("./libs/flux-json-api/src/Adapter/Api/JsonApi.mjs")).JsonApi.new(
    fetch_api
);

const settings_api = (await import("./libs/flux-settings-api/src/Adapter/Api/SettingsApi.mjs")).SettingsApi.new(
    await (await import("./libs/flux-settings-api/src/Adapter/StorageImplementation/getBrowserStorageImplementation.mjs")).getBrowserStorageImplementation(
        "learnplaces-settings",
        "settings",
        null,
        "learnplaces-settings"
  )
);

const localization_api = (await import("./libs/flux-localization-api/src/Adapter/Api/LocalizationApi.mjs")).LocalizationApi.new(
    json_api,
    css_api,
    settings_api
);
await localization_api.init();

const loading_api = (await import("./libs/flux-loading-api/src/Adapter/Api/LoadingApi.mjs")).LoadingApi.new(
    css_api
);
await loading_api.init();

const pwa_api = (await import("./libs/flux-pwa-api/src/Adapter/Api/PwaApi.mjs")).PwaApi.new(
    css_api,
    json_api,
    loading_api,
    localization_api,
    settings_api
);
await pwa_api.init();

await localization_api.addModule(
  `${__dirname}/localization`
);
await localization_api.selectDefaultLanguage();

await pwa_api.initPwa(
  `${__dirname}/appmanifest.webmanifest.json`
);

pwa_api.initServiceWorker(
    `${__dirname}/serviceworker.mjs`,
    async set_hide_confirm => pwa_api.showInstallConfirm(
        set_hide_confirm
    ),
    async () => pwa_api.showUpdateConfirm()
);

const applicationName = "flux-learnplaces";


const layout = await FluxLayoutApi.initialize({
  applicationName: applicationName,
  logEnabled: true,
  definitionsBaseUrl: './contexts/layout/definitions'
});

try {
  await navigator.serviceWorker.register(
    "/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/serviceworker.mjs",
    {
      type: "module"
    });
}
catch (error) {
  console.error(error);
}

await FluxLayoutApi.new(
  FluxLayoutConfig.new(
    true,
    "./libs/layout/definitions",
    "./libs/layout/definitions/css/stylesheet.css",
    "./libs/layout/libs/leaflet/dist/leaflet.css",
  )
);



const repository = await FluxRepositoryApi.initializeOfflineFirstRepository(
  {
    applicationName: applicationName,
    logEnabled: true,
    projectionApiBaseUrl: await getRepositoryApiBaseUrl(),
    definitionsBaseUrl: './libs/repository/definitions',
    projectCurrentUserAddress: 'currentUser/projectObject'
  }
);

const gateway = await FluxGatewayApi.initialize({
  applicationName: applicationName,
  logEnabled: true,
  definitionsBaseUrl: './definitions'
});


await gateway.initActor();
await repository.initActor()


async function getRepositoryApiBaseUrl() {
  console.log(window.navigator.onLine);
  if (window.navigator.onLine === true) {
    const apiBase = await fetch('/goto.php?target=xsrl_1');
    const response = await apiBase.json()
    return response.baseUrl;
  } else {
    return "";
  }

}