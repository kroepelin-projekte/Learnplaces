/** this is an automated generated class **/

import {
  HtmlLayoutRendered
} from '../../../../flux-ui-element-collection/Core/Domain/DomainEvents.mjs';

class FluxUiChannelCuratorAsyncApi {

  /**
   * @var {Service}
   */
  #service;

  /**
   * @var {DomainEventPublisherAdapter}
   */
  #domainEventPublisher;

  static new() {
    return new this();
  }

  /**
   * @private
   */
  constructor() {

  }

  #initReactors() {
    addEventListener('DOMContentLoaded', (event) => {
        this.#onDOMContentLoaded()
      }
    );
  }

  #onDOMContentLoaded() {
    customElements.define("flux-menu", FluxMenuCustomElement);
  }


  #initPublishers() {
    this.#domainEventPublisher.addEventListener(HtmlLayoutRendered.name, (event) => {
        this.#publishDomainEvent(HtmlLayoutRendered.name, event)
      }
    );
  }

  #publishDomainEvent(evenName, event) {
    const channel = new BroadcastChannel("flux-ui-menu/" + evenName)
    channel.postMessage({
      "headers": {},
      "payload": {
        event
      }
    })
  }

}