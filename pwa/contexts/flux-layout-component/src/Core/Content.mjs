import OutboundAdapter from '../Adapters/Api/OutboundAdapter.mjs';

export default class Content {
  /**  @var {string} */
  parentName;
  /**  @var {string} */
  name;
  /**  @var {string} */
  slotName;
  /**  @var {{id: string, value: string}} */
  data;
  /**  @var {string} */
  html;

  #onEvent;


  /**
   *
   * @param {ContentCreatedPayload} payload
   */
  static async createContent(payload, onEvent) {
    const obj = new this(payload);
    //Todo
    obj.#onEvent = onEvent;
    await obj.createHtml();
    return obj;
  }

  constructor({ parentName, name, slotName, data }) {
    this.parentName = parentName;
    this.name = name;
    this.slotName = slotName;
    this.data = data;
  }


  async createHtml() {
    //todo simplify?
    let element = document.createElement('div');
    element.slot = this.slotName;

    const template = await document.getElementById(
      this.name + "-template"
    );
    const contentElement = template.content.cloneNode(true);
    element.appendChild(contentElement)

    element.firstElementChild.id = this.data.id;
    element.firstElementChild.innerHTML = this.data.value;

    //todo not on all elements
    element.addEventListener("click", () => this.#onEvent(
      this.name + "/onClicked", { id: this.data.id }
    ));

    const parent = document.getElementById(this.parentName);

    parent.appendChild(element)
  }
}