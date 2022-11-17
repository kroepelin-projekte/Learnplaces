import OutboundAdapter from '../../../flux-repository/src/Adapters/Api/OutboundAdapter.mjs';
import domainMessage from '../../../flux-repository/src/Core/DomainMessage.mjs';
import DomainMessage from '../../../flux-layout-component/src/Core/DomainMessage.mjs';

export default class Aggregate {
  /**
   * @var {string}
   */
  name = "flux-repository"
  /**
   * @var {string}
   */
  address = "flux-repository"

  #baseUrl = null;

  /**
   * @function()
   */
  #onEvent;
  /**
   * @var {OutboundAdapter}
   */
  #outbounds


  /**
   *
   * @param {OutboundAdapter} outboundsAdapter
   * @return {Aggregate}
   */
  static async initialize(outboundsAdapter) {
    const obj = new this();
    obj.#outbounds = outboundsAdapter;
    obj.#onEvent = await outboundsAdapter.eventStream(obj.address)
    obj.#baseUrl = outboundsAdapter.baseUrl;

    await obj.#applyInitialized(
      DomainMessage.initialized()
    );

    return obj;
  }

  constructor() {}

  /**
   * @param {DomainMessage} initialized
   * @return {Promise<void>}
   */
  async #applyInitialized(initialized) {
    this.#onEvent(initialized.name, initialized.payload)
  }

/*
  #initialize() {
    document.body.insertAdjacentHTML('afterbegin', this.#artefact.template.html);
    const templateId = this.#artefact.template.id;
    const change = (attributeName, attribute) => this.change(attributeName, attribute);

    customElements.define(
      this.#artefact.tagName,
      class extends HTMLElement {
        constructor() {
          super();
          const template = document.getElementById(
            templateId
          ).content;

          const shadowRoot = this.attachShadow({ mode: "open" });
          shadowRoot.appendChild(template.cloneNode(true));
        }

        connectedCallback() {
          change('id', this.id);
          change('dataCacheName', this.getAttribute('dataCacheName'));
        }
      }
    );
  }*/


  /**
   * @param {{address: string, entityName: string}} payload
   * @param {string} replyTo
   */
  async fetchFromOnline(payload, replyTo) {
    console.log(this.#baseUrl + payload.address);
    const response = await fetch(this.#baseUrl + payload.address);
    const responseData = await response.json();
    const responsePayload = await responseData.data

    const entityName = payload.entityName;
    const transformedReplyTo = replyTo.replace("{$entityName}", entityName)

    this.#onEvent(transformedReplyTo, responsePayload)
  }

  /**
   * @param {{address: string}} payload
   */
  async fetchFromOffline(payload) {
    const dataCacheName = this.name;
    const address = payload.address;


    const src = this.#baseUrl + address;


    const cache = await caches.open(dataCacheName);
    const cache_response = await cache.match(src) ?? null;

    let responseData = {};
    if (cache_response !== null) {
      console.log(src + '  from CACHE')
      responseData = await cache_response.json();
    } else {
      console.log(src + '  from ONLINE')

      const response = await fetch(src);
      await cache.put(src, response.clone());
      responseData = await response.json();
    }


    const messagePayload = {
      parentName: payload.parentName,
      slotName: payload.slotName,
      name: payload.name,
      data: await responseData.data
    }

    this.#onEvent("fetched", messagePayload);
  }



}
