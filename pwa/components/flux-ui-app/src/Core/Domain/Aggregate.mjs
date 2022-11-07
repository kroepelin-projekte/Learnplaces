import domainMessage from './DomainMessage.mjs';
import artefact from './Artefact.mjs';

export default class Aggregate {
  /**
   * @var {{id: string, tagName: string, templateId: string, template: string}}
   */
  fluxAppElement;
  /**
   * @var {function}
   */
  #publish;


  /**
   * @param {function} publishDomainEventCallback
   * @return {Aggregate}
   */
  static create(publishDomainEventCallback) {
    const obj = new this(publishDomainEventCallback);
    obj.initialize();
    obj.#iniSlotChangedPublisher()

    obj.#publish(domainMessage.newCreated({
        id: obj.fluxAppElement.id, tagName: obj.fluxAppElement.tagName
      }
    ));

    return obj;
  }

  constructor(publishDomainEventCallback) {
    this.fluxAppElement = artefact.newFluxAppElement()
    this.#publish = publishDomainEventCallback;
  }

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
  }

  /**
   * @param {FluxComponentApi} componentApi
   * @return {void}
   */
  async initComponent(componentApi) {
    //guard
    if (customElements.get(componentApi.tagName)) {
      console.error("element already registered" + componentApi.tagName);
      return;
    }
    const baseUrl = document.getElementById("myBase");
    const ApiClass = await this.importApiClass(baseUrl.href + componentApi.src)
    ApiClass.initialize(componentApi.tagName);

  }

  async importApiClass(url) {
    const ApiClass = await(await(import(url)));
    return ApiClass.default;
  }
}