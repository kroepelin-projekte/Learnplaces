import FluxAppApi from './modules/flux-app/src/Adapters/Api/FluxAppApi.mjs';
import FluxMessageStreamApi
  from './modules/flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs';
import FluxRepositoryApi from './modules/flux-repository/src/Adapters/Api/FluxRepositoryApi.mjs';

try {
  await navigator.serviceWorker.register("/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/serviceworker.mjs", {
    type: "module"
  });
} catch (error) {
  console.error(error);
}
FluxAppApi.initialize(
  {
    name: 'flux-app-learnplaces',
    behaviorSchema: './behaviors/schemas/flux-app-learnplaces.asyncapi.json',
    messageStream: FluxMessageStreamApi.initialize(true),
    repository: FluxRepositoryApi.initialize()
  }
);


/*DomMessages.new().onDomContentLoaded((message) => this.#onDOMContentLoaded(message))
this.#broadcastChannelBinding.addListener(
  domainMessage.slotchanged,
  (message) => this.#onSlotChanged(message.data)
)*/
