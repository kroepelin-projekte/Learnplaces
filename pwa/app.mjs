import FluxAppApi from './contexts/flux-app/src/Adapters/Api/FluxAppApi.mjs';
import FluxRepositoryApi from './contexts/flux-repository/src/Adapters/Api/FluxRepositoryApi.mjs';
import FluxLayoutComponentApi
  from "./contexts/flux-layout/src/Adapters/Api/FluxLayoutComponentApi.mjs";

const applicationName = "flux-learnplaces";

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

await FluxAppApi.initialize(applicationName, true);
await FluxLayoutComponentApi.initialize(applicationName, true);
await FluxRepositoryApi.initializeOfflineFirstRepository(
  applicationName,
  true,
  await getRepositoryApiBaseUrl()
);

async function getRepositoryApiBaseUrl() {
  console.log(window.navigator.onLine);
  if (window.navigator.onLine === true) {
    const apiBase = await fetch('/goto.php?target=xsrl_1&client_id=default');
    const response = await apiBase.json()
    return response.data.baseUrl;
  } else {
    return "";
  }

}