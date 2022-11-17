export default class Element {
  /**  @var {string} */
  name;
  /**  @var {string} */
  address;

  /**
   *
   * @param {ElementCreatedPayload} payload
   */
  static async createElement(payload) {
    const obj = new this(payload);
    await obj.createHtmlElement();
    return obj;
  }

  /**
   *
   * @param {SlotElementCreatedPayload} payload
   */
  static async createSlotElement(payload) {
    const obj = new this(payload);
    await obj.createHtmlElement(payload.parentName, payload.slotName);
    return obj;
  }

  constructor({ name, address }) {
    this.name = name;
    this.address = address;
  }


  async createHtmlElement(parentName = null, slotName = null) {
    const linkStyleSheet = document.getElementById('flux-layout-style');
    const styleElement = document.createElement('style');
    styleElement.innerHTML = await (await fetch(linkStyleSheet.href)).text();
    const name = this.name;

    customElements.define(
      name,
      class extends HTMLElement {

        constructor() {
          super();
          const templateContent = document.getElementById(
            name + "-template"
          ).content;

          if(slotName !== null) {
            templateContent.slot = slotName;
          }

          const shadowRoot = this.attachShadow({ mode: "open" });
          shadowRoot.appendChild(templateContent.cloneNode(true));
          shadowRoot.append(styleElement);
        }

        connectedCallback() {

        }
      }
    );

    const element = document.createElement(this.name);

    if(parentName) {
        const parent = document.getElementById(parentName);
        element.id = slotName;
        element.slot = slotName;
        parent.appendChild(element)
        return;
    }

    element.id = this.name;
    document.body.append(element)
  }
}