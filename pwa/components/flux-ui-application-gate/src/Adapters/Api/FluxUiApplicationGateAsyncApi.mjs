import Service from '../../Core/Ports/Service.mjs';
import { ContextInitialized } from '../../Core/Domain/DomainEvents.mjs';
import { Create, InitializeLayoutContext } from '../../Core/Ports/Commands.mjs';
import FluxUiServiceWorkerApi
  from '../../../../flux-ui-service-worker/src/Adapters/Api/FluxUiServiceWorkerApi.mjs';


export default class FluxUiApplicationGateAsyncApi {

  /**
   * @var {Configs}
   */
  #configs;

  /**
   * @var {Service}
   */
  #service;

  static create(configs) {
    return new this(configs);
  }

  /**
   * @param {FluxUiContextKeeperConfigs} configs
   * @private
   */
  constructor(configs) {
    this.#configs = configs;
    this.#service = Service.new(
      configs.domainEventPublisher
    );
    this.#initReactors();
  }

  #initReactors() {
    window.addEventListener('DOMContentLoaded', (event) => {
      this.#onDOMContentLoaded()
    });
  }

  #onDOMContentLoaded() {
    this.#service.create(Create.create(this.#configs.componentId));
    this.#service.initializeLayoutContext(InitializeLayoutContext.create(this.#configs.contextKeeperApiSrc));

    //todo
    FluxUiServiceWorkerApi.create();
  }


  #initializePublishers() {
    this.#configs.domainEventPublisher.addEventListener(ContextInitialized.name, (event) => {
        this.#publishDomainEvent(ContextInitialized.name, event)
      }
    );
  }

  #publishDomainEvent(evenName, event) {
    const channel = new BroadcastChannel(this.#configs.componentId + evenName)
    channel.postMessage({
      "headers": {},
      "payload": {
        event
      }
    })
  }

}