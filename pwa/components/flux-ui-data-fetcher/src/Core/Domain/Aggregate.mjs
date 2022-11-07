import domainMessage from './DomainMessage.mjs';
import artefact from './Artefact.mjs';
import dataFetcherType from './DataFetcherTypes/DataFetcherType.mjs';

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

  fetchData() {
      const element = document.getElementById(this.id);

      //getEndpointConfiguration @see index.html

      //fetch data from Offline if exists

      //publish data to
      //this.#publish()

      //update offline storage
  }

}