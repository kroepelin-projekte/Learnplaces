import domainMessage from './DomainMessage.mjs';
import OutboundAdapter from '../Adapters/Api/OutboundAdapter.mjs';
import State from './State.mjs';

export default class Aggregate {
  /**
   * @var {string}
   */
  #name;
  /**
   * @var {string}
   */
  #id;
  /**
   * @var {string}
   */
  #stylesheet;
  /**
   * @var {{id: string, html: string}}
   */
  #template;
  /**
   * @var {State}
   */
  #state;
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
   * @param payload
   * @param replyTo
   * @return {Aggregate}
   */
   static async initialize(payload, replyTo) {
    const obj = new this(payload, replyTo);
    obj.#state = State.new();
    obj.#outbounds = OutboundAdapter.new()
    obj.#template = await obj.#outbounds.loadTemplate(obj.#name);
    obj.#onEvent =  await obj.#outbounds.onEvent(obj.#name)
    obj.#onEvent(replyTo,
      domainMessage.initialized({
        name: obj.#name
      })
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



  /**
   * @param {{name: string}} payload
   * @param {string} replyTo
   */
  async defineCustomHtmlElement(payload, replyTo) {
    document.body.insertAdjacentHTML('afterbegin', this.#template.html);
    const templateId = this.#template.id;

    const defineId = (id) => {
      this.defineId(id)
    };
    const changeAttribute = (attributeName, attribute) => this.changeAttribute(attributeName,
      attribute);
    const stylesheetText = await (await this.#outbounds.importCss());

    const styleElement = document.createElement('style');
    styleElement.innerHTML = stylesheetText;

    customElements.define(
      this.#name,
      class extends HTMLElement {
        constructor() {
          super();
          const template = document.getElementById(
            templateId
          ).content;

          const shadowRoot = this.attachShadow({ mode: "open" });
          shadowRoot.appendChild(template.cloneNode(true));

          shadowRoot.append(styleElement);
        }

        connectedCallback() {
          defineId('id', this.id);
          if (this.hasAttributes()) {
            for (const name of this.getAttributeNames()) {
              const value = this.getAttribute(name);
              changeAttribute('name', value);
            }
          }
        }
      }
    );
    this.#onEvent(replyTo, payload);
  }

  defineId(id) {
    this.#id = id
  }

  async changeAttribute(attributeName, attribute) {
    const updateCache = async (attributeName, attribute) => {
      const cache = await caches.open(this.#name);
      await cache.put(attributeName, attribute);
      this.#onEvent(this.#name + "/" + attributeName + "changed", attribute)
    }
    this.#state.change(attributeName, attribute, updateCache);
  }


}