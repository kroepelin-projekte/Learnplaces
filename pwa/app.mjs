import FluxAppApi from './contexts/flux-app/src/Adapters/Api/FluxAppApi.mjs';
import FluxMessageStreamApi
  from './contexts/flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs';
import FluxRepositoryApi from './contexts/flux-repository/src/Adapters/Api/FluxRepositoryApi.mjs';
import FluxLayoutComponentApi from "./contexts/flux-layout-component/src/Adapters/Api/FluxLayoutComponentApi.mjs";
import InitializeFluxLayoutComponent
    from "./contexts/flux-layout-component/src/Adapters/Api/InitializeFluxLayoutComponent.mjs";
/*
try {
  await navigator.serviceWorker.register("/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/serviceworker.mjs", {
    type: "module"
  });
} catch (error) {
  console.error(error);
}*/
FluxAppApi.initialize(
  {
    name: 'flux-app-learnplaces',
    behaviorSchema: './behaviors/schemas/flux-app-learnplaces.asyncapi.json',
    messageStream: FluxMessageStreamApi.initialize(true)
  }
);

FluxLayoutComponentApi.initialize(InitializeFluxLayoutComponent.new(
    './contexts/flux-layout-component/behaviors'
));


/*DomMessages.new().onDomContentLoaded((message) => this.#onDOMContentLoaded(message))
this.#broadcastChannelBinding.addListener(
  domainMessage.slotchanged,
  (message) => this.#onSlotChanged(message.data)
)*/
