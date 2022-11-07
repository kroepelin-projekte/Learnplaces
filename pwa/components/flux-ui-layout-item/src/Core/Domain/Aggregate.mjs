import domainMessage from './DomainMessage.mjs';
import artefact from './Artefact.mjs';
import Primer from './Primer/Primer.mjs';

export default class Aggregate {
  /**
   * @var {FluxLayoutItem}
   */
  #fluxLayoutItem;
  /**
   * @var {function}
   */
  #publish;


  static create(tagName, publishDomainEventCallback) {
    const obj = new this(tagName, publishDomainEventCallback);
    obj.#initialize();
    obj.#publish(domainMessage.newCreated(obj.#fluxLayoutItem))
    return obj;
  }


  constructor(tagName, publishDomainEventCallback) {
    this.#fluxLayoutItem = artefact.newFluxLayoutItem(tagName)
    this.#publish = publishDomainEventCallback;
  }


  #initialize() {
    document.body.insertAdjacentHTML('afterbegin', this.#fluxLayoutItem.template);

    const templateId = this.#fluxLayoutItem.templateId;
    customElements.define(
      this.#fluxLayoutItem.tagName,
      class extends HTMLElement {
        constructor() {
          super();
          const template = document.getElementById(
            templateId
          ).content;

          const shadowRoot = this.attachShadow({ mode: "open" });
          shadowRoot.appendChild(template.cloneNode(true));

          const styles = document.createElement("style");
          styles.innerHTML = Primer
          shadowRoot.append(styles);
        }
      }
    );
  }
}