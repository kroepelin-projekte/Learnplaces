import domainMessage from './DomainMessage.mjs';
import artefact from './Artefact.mjs';
import dataFetcherType from './DataFetcherTypes/DataFetcherType.mjs';

const DATA_CACHE_NAME = "learnplaces-data";

export default class Aggregate  {
  /**
   * @var {string} id
   */
  id;

  /**
   * @var {FluxDataFetcherType}
   */
  #fluxDataFetcherType;
  /**
   * @var {function}
   */
  #publish;


  static create(tagName, publishDomainEventCallback) {
    const obj = new this(tagName, publishDomainEventCallback);
    obj.#initialize();
    obj.#publish(domainMessage.newCreated({id: tagName,tagName: tagName})) //todo
    return obj;
  }

  static fromId(id, tagName, publishDomainEventCallback) {
    const obj =  new this(tagName, publishDomainEventCallback);
    obj.id = id;
    return id;
  }


  constructor(tagName, publishDomainEventCallback) {
    this.#fluxDataFetcherType = artefact.newDataFetcherType(tagName)
    this.#publish = publishDomainEventCallback;
  }


  #initialize() {
    document.body.insertAdjacentHTML('afterbegin', this.#fluxDataFetcherType.template);

    const templateId = this.#fluxDataFetcherType.templateId;
    customElements.define(
      this.#fluxDataFetcherType.tagName,
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

  async fetchDataFromOffline(payload) {
    const cache = await caches.open(DATA_CACHE_NAME);

    const cache_response = await cache.match(payload.src) ?? null;

    if (cache_response !== null) {
      this.#publish(await cache_response.json());
      return;
    }

    const response = await fetch(payload.src);

    cache.put(payload.src, response.clone());

    this.#publish(await response.json());
  }

}
