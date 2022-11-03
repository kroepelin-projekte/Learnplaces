import FluxUiApplicationGateAsyncApi from './components/flux-ui-application-gate/src/Adapters/Api/FluxUiApplicationGateAsyncApi.mjs';
import Configs from './components/flux-ui-application-gate/src/Adapters/Api/Configs.mjs';

const app = FluxUiApplicationGateAsyncApi.create(
  Configs.create(
    'fluxlabs',
    'flux-learn-places-pwa'
  )
)