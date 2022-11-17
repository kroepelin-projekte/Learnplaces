import DomainMessage from "./DomainMessage.mjs";
import Element from './Element.mjs';
import Content from './Content.mjs';

export default class Aggregate {
  /**
   * @var {string}
   */
  name = "flux-layout-component"
  /**
   * @var {string}
   */
  address = "flux-layout-component"


  /**
   * @var {string}
   */
  #stylesheet;
  /**
   * @var {{id: string, html: string}}
   */
  #template;
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

    await obj.#applyInitialized(
      DomainMessage.initialized()
    );

    return obj;
  }

  /** @private */
  constructor() {
  }

  /**
   * @param {DomainMessage} initialized
   * @return {Promise<void>}
   */
  async #applyInitialized(initialized) {
    this.#onEvent(initialized.name, initialized.payload)
  }

  /**
   * @param {{name: string}} payload
   * @return {Promise<void>}
   */
  async createElement(payload) {
    await this.loadTemplate(payload.name + "-template")
    await this.#applyElementCreated({
        name: payload.name,
        address: this.address + "/" + payload.name
      }
    )
  }

  /**
   * @param {ElementCreatedPayload} payload
   * @return {Promise<void>}
   */
  async #applyElementCreated(payload) {
    await Element.createElement(payload);
    this.#onEvent(payload.name + "/" + DomainMessage.elementCreated, payload);
  }

  /**
   * @param {{parentName: string, slotName: string, name: string}} payload
   * @return {Promise<void>}
   */
  async createSlotElement(payload) {
    await this.loadTemplate(payload.name + "-template")
    await this.#applySlotElementCreated({
        parentName: payload.parentName,
        slotName: payload.slotName,
        name: payload.name,
        address:  this.address + "/" + payload.slotName
      }
    )
  }

  /**
   * @param {SlotElementCreatedPayload} payload
   * @return {Promise<void>}
   */
  async #applySlotElementCreated(payload) {
    await Element.createSlotElement(payload);
    this.#onEvent(payload.slotName + "/" + DomainMessage.slotElementCreated, payload);
  }

  /**
   * @param {{parentName: string, slotName: string, name: string, data: {id: string, value: string}}} payload
   * @return {Promise<void>}
   */
  async createContent(payload) {
    console.log(payload)
    await this.loadTemplate(payload.name + "-template")
    await this.#applyContentCreated({
        parentName: payload.parentName,
        slotName: payload.slotName,
        name: payload.name,
        data: payload.data
      }
    )
  }

  /**
   * @param {ContentCreatedPayload} payload
   * @return {Promise<void>}
   */
  async #applyContentCreated(payload) {
    await Content.createContent(payload, this.#onEvent);
    this.#onEvent(payload.slotName + "/" + DomainMessage.contentCreated, payload);
  }

  async createContents(payload) {
    const items = payload.data.items;
    Object.entries(items).forEach(([id, item]) => {
        this.createContent(
          {
            parentName: payload.parentName,
            slotName: payload.slotName,
            name: payload.name,
            data: item
          }
        )
    });
  }


  async loadTemplate(templateName) {

    console.log(templateName);

    if(document.getElementById(templateName)) {
      return;
    }
    const template = await this.#outbounds.loadTemplate(templateName)
    document.body.appendChild(template);
  }



  async replaceSlotData(slotName, payload, replyTo) {
    const slotTemplateDefinition = await this.#outbounds.loadTemplate(slotName)
    const slotTemplateId = slotTemplateDefinition.id

    if (document.getElementById(slotTemplateId) === null) {
      const slotTemplateHtml = slotTemplateDefinition.html;
      await document.body.insertAdjacentHTML('afterbegin', slotTemplateHtml);
    }

    const element = await document.getElementById(this.name);
    const slotTemplate = document.getElementById(slotTemplateId);

    const items = payload.items;
    const childElement = slotTemplate.content.firstChild.cloneNode(true);

    Object.entries(items).forEach(([id, item]) => {
      childElement.id = item.id;
      childElement.innerHTML = item.value;


      if (slotTemplateDefinition.events.hasOwnProperty('onClick')) {
        const adressDefinition = slotTemplateDefinition.events.onClick.address;
        const adress = adressDefinition.replace("{$name}", slotName);

        element.addEventListener("click", () => this.#outbounds.onEvent(this.name)(
          adress, { id: item.id }
        ));

      }
      element.appendChild(childElement.cloneNode(true))


    });


    this.#onEvent(replyTo, items)


  }


}