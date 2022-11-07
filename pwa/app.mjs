import FluxBroadcastChannelApi from './components/flux-ui-broadcast-channel/src/Adapters/Api/FluxBroadcastChannelApi.mjs';
import FluxAppApi from './components/flux-ui-app/src/Adapters/Api/FluxAppApi.mjs';
import FluxUiServiceWorkerApi from './components/flux-ui-service-worker/src/Adapters/Api/FluxUiServiceWorkerApi.mjs';
FluxUiServiceWorkerApi.create();
FluxAppApi.initialize();
