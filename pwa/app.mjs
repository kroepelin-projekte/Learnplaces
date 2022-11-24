import FluxGatewayApi from './contexts/gateway/src/Adapters/Api/FluxGatewayApi.mjs';
import FluxRepositoryApi from './contexts/repository/src/Adapters/Api/FluxRepositoryApi.mjs';
import FluxLayoutApi
  from "./contexts/layout/src/Adapters/Api/FluxLayoutApi.mjs";

const applicationName = "flux-learnplaces";
/*
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
*/
const layout = await FluxLayoutApi.initialize({
  applicationName: applicationName,
  logEnabled: true,
  definitionsBaseUrl: './contexts/layout/definitions'
});
const repository = await FluxRepositoryApi.initializeOfflineFirstRepository(
  {
    applicationName: applicationName,
    logEnabled: true,
    projectionApiBaseUrl: await getRepositoryApiBaseUrl(),
    definitionsBaseUrl: './contexts/repository/definitions'
  }
);

const gateway = await FluxGatewayApi.initialize({
  applicationName: applicationName,
  logEnabled: true,
  definitionsBaseUrl: './definitions'
});


await gateway.initActor();
await layout.initActor();
await repository.initActor()


async function getRepositoryApiBaseUrl() {
  console.log(window.navigator.onLine);
  if (window.navigator.onLine === true) {
    const apiBase = await fetch('/goto.php?target=xsrl_1&client_id=default');
    const response = await apiBase.json()
    return response.baseUrl;
  } else {
    return "";
  }

}