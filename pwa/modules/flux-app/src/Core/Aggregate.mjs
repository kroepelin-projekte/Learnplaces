import OutboundAdapter from '../Adapters/Api/OutboundAdapter.mjs';

export default class Aggregate {

  /**
   * @var {string}
   */
  #name;
  /**
   * @var {OutboundAdapter}
   */
  #outbounds;
  /**
   * @var
   */
  #onEvent
  /**
   * @var {{defineCustomHtmlElement(payload): void}}
   */
  #publish


  /**
   * @param {name, messageStream} payload
   * @param replyTo
   * @return {Aggregate}
   */
  static initialize(payload, replyTo) {
    return new this(payload, replyTo);
  }

  /**
   * @param {name, messageStream} payload
   * @param replyTo
   * @private
   */
  constructor(payload, replyTo) {
    this.#name = payload.name;
    this.#outbounds = OutboundAdapter.new();

    this.#publish = this.#outbounds.publish();
    this.#onEvent = this.#outbounds.onEvent(this.#name);
    this.#onEvent(replyTo, {})
  }


  /**
   * @param {{names: string[]}} payload
   * @param  {string} replyTo
   * @return {Promise<void>}
   */
  async initializeLayoutComponents(payload, replyTo) {
    payload.names.forEach(
      name => {
          this.#outbounds.initializeLayoutComponent({ name: name })
      }
    );
    this.#onEvent(replyTo, payload);
  }

  /**
   * @param {{names: string[]}} payload
   * @param  {string} replyTo
   * @return {void}
   */
  async initializeRepositories(payload, replyTo) {
    payload.names.forEach(
      name => {
        this.#outbounds.initializeRepositories({ name: name })
      }
    );
    this.#onEvent(replyTo, payload);
  }

  /**
   * @param {{}} payload
   * @param  {string} replyTo
   * @return {void}
   */
  async byPass(payload, replyTo) {
    this.#onEvent(replyTo, payload)
  }


  /**
   * @param {function} publishDomainEventCallback
   * @return {Aggregate}
   */
  /*static create(publishDomainEventCallback) {
    const obj = new this(publishDomainEventCallback);
    obj.initialize();
    obj.#iniSlotChangedPublisher()

    obj.#onEvent(domainMessage.newCreated({
        id: obj.fluxAppElement.id, tagName: obj.fluxAppElement.tagName
      }
    ));

    return obj;
  }*/


  /*
    initialize() {
      document.body.insertAdjacentHTML('afterbegin', this.fluxAppElement.template);

      const templateId = this.fluxAppElement.templateId;
      customElements.define(
        this.fluxAppElement.tagName,
        class extends HTMLElement {
          constructor() {
            super();
            const template = document.getElementById(
              templateId
            ).content;
            const shadowRoot = this.attachShadow({ mode: "open" });
            shadowRoot.appendChild(template.cloneNode(true));
          }
        }
      );
    }

    #iniSlotChangedPublisher() {
      const slot = document.getElementById(this.fluxAppElement.id).shadowRoot.querySelector("slot");
      slot.addEventListener('slotchange', e => {
        for (const changedSlot of slot.assignedElements()) {
          this.#publish(domainMessage.newSlotChanged(artefact.newFluxSlotElement(changedSlot.slot,
            changedSlot.value)))
        }
      });
    }*/

  /**
   * @param {FluxComponentApi} componentApi
   * @return {void}
   */

  /*
  async initComponent(componentApi) {
    //guard
    const baseUrl = document.getElementById("myBase");
    const ApiClass = await this.importApiClass(baseUrl.href + componentApi.src)
    if(componentApi.tagName !== null) {
      if (customElements.get(componentApi.tagName)) {
        console.error("element already registered" + componentApi.tagName);
        return;
      }
      ApiClass.initialize(componentApi.tagName);
      return;
    }
    ApiClass.initialize();
  }

  async importApiClass(url) {
    const ApiClass = await(await(import(url)));
    return ApiClass.default;
  }*/
}