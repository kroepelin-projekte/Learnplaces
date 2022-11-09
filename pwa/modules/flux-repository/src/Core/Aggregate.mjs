import artefact from './artefact.json' assert { type: 'json' };

const DATA_CACHE_NAME = "learnplaces-data";

export default class Aggregate {
  /**
   * @var {function(
   *   address:string, payload:object
   * )}
   */
  #publish;

  #artefact;

  static create(publishDomainMessageCallback, replyTo) {
    const obj = new this(publishDomainMessageCallback);
    publishDomainMessageCallback(replyTo, { tagName: artefact.tagName });
    return obj;
  }

  /**
   * @private
   * @param publishDomainMessageCallback
   */
  constructor(publishDomainMessageCallback) {
    this.#publish = publishDomainMessageCallback;
    this.#artefact = artefact;
    this.#initialize();
  }


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
  }

  change(attributeName, attribute) {
    this.#artefact[attributeName] = attribute;
  }

  /**
   * @param {{baseUrl: string}} payload
   * @param {string} replyTo
   */
  changeBaseUrl(payload, replyTo) {
    this.#artefact.baseUrl = payload.baseUrl;
    this.#publish(
      replyTo,
      { baseUrl: this.#artefact.baseUrl }
    )
  }

  /**
   * @param {{address: string}} payload
   * @param {string} replyTo
   */
  async fetchDataFromOnline(payload, replyTo) {
    const response = await fetch(payload.address);
    const responseData = await response.json();
    const responsePayload = responseData.data;

    this.#publish(replyTo, responsePayload)
  }

  /**
   * @param {{address: string}} payload
   * @param {string} replyTo
   */
  async fetchDataFromOffline(payload, replyTo) {
    if (this.#artefact.dataCacheName === null) {
      this.#publish(replyTo, { error: "no cache defined" });
      return;
    }

    const dataCacheName = this.#artefact.dataCacheName;
    const address = payload.address;

    let src = "";
    if (this.#artefact.baseUrl !== null) {
      src = this.#artefact.baseUrl + address;
    } else {
      src = address;
    }

    const cache = await caches.open(dataCacheName);
    const cache_response = await cache.match(address) ?? null;

    if (cache_response !== null) {
      const responseData = await cache_response.json();
      this.#publish(replyTo, await responseData.data);
      return;
    }

    const response = await fetch(src);
    const responseData = await response.json();
    this.#publish(replyTo, await responseData.data);

    await cache.put(address, responseData.clone());
  }

  /**
   * @return {string|null}
   */
  #getDataCacheName() {
    return this.#artefact.id;
  }

}
