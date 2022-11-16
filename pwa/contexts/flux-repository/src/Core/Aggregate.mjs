import OutboundAdapter from '../../../flux-repository/src/Adapters/Api/OutboundAdapter.mjs';
import domainMessage from '../../../flux-repository/src/Core/DomainMessage.mjs';

export default class Aggregate {
  /**
   * @var {string}
   */
  #name;
  /**
   * @var {string}
   */
  #baseUrl = "";
  /**
   * @var {string}
   */
  #id;
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
   * @param {{name:string}} payload
   * @param replyTo
   * @return {Aggregate}
   */
  static async initialize(payload, replyTo) {
    const obj = new this(payload, replyTo);

    obj.#outbounds = OutboundAdapter.new()
    obj.#onEvent =  await obj.#outbounds.onEvent(obj.#name)
    obj.#onEvent(replyTo,
      domainMessage.initialized(payload)
    )
    return obj;
  }

  /**
   * @param {{name: string}} payload
   * @param {string} replyTo
   */
  constructor(payload, replyTo) {
    this.#name = payload.name;
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
   * @param {{baseUrl: string}} payload
   * @param {string} replyTo
   */
  changeBaseUrl(payload, replyTo) {
    this.#baseUrl = payload.baseUrl;
    this.#onEvent(
      replyTo,
      payload
    )
  }

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
   * @param {string} replyTo
   */
  async fetchFromOffline(payload, replyTo) {
    const dataCacheName = this.#name;
    const address = payload.address;

    const entityName = payload.entityName;
    const transformedReplyTo = replyTo.replace("{$entityName}", entityName)

    const src = this.#baseUrl + address;


    //const cache = await caches.open(dataCacheName);
    //const cache_response = await cache.match(address) ?? null;

    /*if (cache_response !== null) {
      const responseData = await cache_response.json();
      this.#onEvent(transformedReplyTo, await responseData.data);
      return;
    }*/

    console.log(src);
    const response = await fetch(src);
    const responseData = await response.json();
    this.#onEvent(transformedReplyTo, await responseData.data);

    //await cache.put(address, responseData.clone());
  }



}
