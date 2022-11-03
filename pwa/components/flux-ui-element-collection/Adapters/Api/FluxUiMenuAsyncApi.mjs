/** this is an automated generated class, related to app learnplaces **/

import { HtmlLayoutRendered } from '../../Core/Domain/DomainEvents.mjs';

export default class FluxUiMenuAsyncApi {

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
    this.#domainEventPublisher.addEventListener(HtmlLayoutRendered.getName(), (event) => {
        this.#publishDomainEvent(HtmlLayoutRendered.getName(), event)
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