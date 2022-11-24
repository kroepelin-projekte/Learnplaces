import {
  created, slotDataChanged
} from './Behaviors.mjs';
import MapElement from './Elements/MapElement.mjs';
import AppElement from './Elements/AppElement.mjs';

export default class Actor {
  /**
   * @var {string}
   */
  #appName;
  /**
   * @var {ShadowRoot}
   */
  #shadowRoot;
  /**
   * @function()
   */
  #publish;
  /**
   * @function()
   */
  #template;


  /**
   * @private
   */
  constructor(publish, template) {
    this.#publish = publish;
    this.#template = template;
  }

  static async new(appName, publish, template) {
    const obj = new Actor(publish, template);
    await AppElement.initialize(appName);
    await MapElement.initialize();

    const element = document.createElement(appName)
    element.id = appName;
    await document.body.appendChild(element);
    obj.#shadowRoot = await element.shadowRoot;

    await obj.createElement(appName + "/" + "layout", "div");

    return obj;
  }


  async createElement(id, tag) {
    const element = document.createElement(tag);
    element.id = id;
    await this.#shadowRoot.appendChild(element);
    await this.#applyCreated(
      created(id)
    );
  }

  /**
   * @param {CreatedEvent} payload
   * @return {void}
   */
  async #applyCreated(payload) {
    this.#publish(payload.id + "/" + created.name, payload)
  }


  /**
   * @param {AppendTemplateContent} payload
   * @return {void}
   */
  async appendTemplateContent(payload) {
    const id = payload.parentId + "/" + payload.templateName;

    if (this.#shadowRoot.getElementById(id)) {
      return;
    }

    const templateId = payload.templateName + "-template";
    await this.#loadTemplate(templateId)
    const templateContent = await this.#shadowRoot.getElementById(templateId)
    .content
    .cloneNode(true)
    const element = templateContent.children[0]

    let slots = element.querySelectorAll('slot');
    const slotNames = [];
    [].forEach.call(slots, function (slot) {
      // do whatever
      slotNames.push(slot.name)
    });

    const div = document.createElement('div');
    div.attachShadow({ mode: "open" })
    div.id = id;

    const shadowRoot = await div.shadowRoot;

    if (payload.slotName) {
      div.slot = payload.slotName;
    }
    shadowRoot.appendChild(element);


    const linkStyleSheet = document.getElementById('flux-layout-style');
    const styleElement = document.createElement('style');
    styleElement.innerHTML = await (await fetch(linkStyleSheet.href)).text();
    shadowRoot.appendChild(styleElement);
    console.log(payload.parentId);
    this.#shadowRoot.getElementById(payload.parentId).appendChild(div)

    await this.#applyCreated(
      created(
        id,
        slotNames
      )
    )
  }

  async changeSlotData(payload) {
    const parentId = payload.parentId;

    console.log(parentId);
    const parentElement = await this.#shadowRoot.getElementById(parentId);
    const shadowRoot = parentElement.shadowRoot;

    const data = payload;


    for (const [slotName, slotData] of Object.entries(data)) {
      const slots = shadowRoot.querySelectorAll('slot[name=' + slotName + ']');
      if (slots[0]) {
        const slotDefinition = slots[0];
        let addOnClickEvent = false;
        if (slotDefinition.hasAttribute('add-on-click-event')) {
          addOnClickEvent = slotDefinition.getAttribute('add-on-click-event')
        }

        const elementContainerId = parentId + "/" + slotName;
        console.log(slotDefinition);
        //TODO extract in functions
        //single slotItem
        if (slotDefinition.getAttribute('slot-value-type') === "key-value-item") {
          const slotItem = slotData;
          let elementContainer = null;
          let element = null;
          elementContainer = this.#shadowRoot.getElementById(elementContainerId);
          if (elementContainer) {
            element = elementContainer.children[0];
          }
          if (elementContainer === null) {
            const templateId = slotDefinition.getAttribute('template-id');
            await this.#loadTemplate(templateId);
            const templateContent = this.#shadowRoot.getElementById(templateId)
            .content
            .cloneNode(true)
            elementContainer = document.createElement('div');
            elementContainer.id = elementContainerId;
            elementContainer.slot = slotName;
            element = templateContent.children[0];
            elementContainer.appendChild(element);
            parentElement.appendChild(elementContainer);
          }
          element.textContent = slotItem.value
          element.id = elementContainerId + "/" + slotItem.id;
          if (addOnClickEvent) {
            element.addEventListener("click", () => this.#publish(
              slotItem.parentId + "/" + slotName + "/clicked", {  id: slotItem.id  }
            ));
          }

          await this.#applySlotDataChanged(slotDataChanged(
            elementContainerId, [slotItem.id]
          ))
          return;
        }

        //TODO extract in functions
        if (slotDefinition.getAttribute('slot-value-type') === "key-value-list") {
          let idList = [];
          const slotItemList = slotData;
          let elementContainer = null;
          let element = null;

          elementContainer = this.#shadowRoot.getElementById(elementContainerId);
          if (elementContainer) {
            elementContainer.remove();
          }

          const templateId = slotDefinition.getAttribute('template-id');
          await this.#loadTemplate(templateId);
          const templateContent = this.#shadowRoot.getElementById(templateId)
          .content
          .cloneNode(true)
          elementContainer = document.createElement('div');
          elementContainer.id = elementContainerId;
          elementContainer.slot = slotName;

          Object.entries(slotItemList).forEach(([itemKey, slotItem]) => {

            element = templateContent.children[0].cloneNode(true);

            element.textContent = slotItem.value
            element.id = elementContainerId + "/" + +slotItem.id;
            idList.push(slotItem.id);

            if (addOnClickEvent) {

              const data = {};
              data[slotItem.idType] = slotItem.id

              element.addEventListener("click", () => this.#publish(
                elementContainerId + "/clicked", { data }
              ));
            }
            elementContainer.appendChild(element)
          });
          parentElement.appendChild(elementContainer);

          await this.#applySlotDataChanged(slotDataChanged(
            elementContainerId, idList
          ))
          return;
        }

        //TODO extract in functions
        if (slotDefinition.getAttribute('slot-value-type') === "object") {
          let idList = [];
          const object = slotData;
          let elementContainer = null;

          elementContainer = this.#shadowRoot.getElementById(elementContainerId);
          if (elementContainer) {
            elementContainer.remove();
          }

          const templateId = slotDefinition.getAttribute('template-id');
          await this.#loadTemplate(templateId);
          const templateContent = this.#shadowRoot.getElementById(templateId)
          .content
          .cloneNode(true)
          elementContainer = document.createElement('div');
          elementContainer.id = elementContainerId;
          elementContainer.slot = slotName;
          const slotObjetId = elementContainerId;

            Object.entries(object).forEach(([propertyName, property]) => {
                const queryResult = templateContent.querySelectorAll('[name=' + propertyName + ']');
              queryResult.forEach(function(element) {
                element.setAttribute('content', property);
                elementContainer.appendChild(element)
                console.log(element);
              });

            });

          parentElement.appendChild(elementContainer);

          await this.#applySlotDataChanged(slotDataChanged(
            elementContainerId, [slotObjetId]
          ))
          return;
        }


          if (slotDefinition.getAttribute('slot-value-type') === "object-list") {
            let idList = [];
            const objectList = slotData;
            let elementContainer = null;

            console.log(objectList);

            elementContainer = this.#shadowRoot.getElementById(elementContainerId);
            if (elementContainer) {
              elementContainer.remove();
            }


            const templateId = slotDefinition.getAttribute('template-id');
            await this.#loadTemplate(templateId);
            const templateContent = this.#shadowRoot.getElementById(templateId)
            .content
            .cloneNode(true)
            elementContainer = document.createElement('div');
            elementContainer.id = elementContainerId;
            elementContainer.slot = slotName;

            Object.entries(objectList).forEach(([index, object]) => {
              const slotObjetId = elementContainerId + "/" + index;
              const div = document.createElement('div');
              div.id = slotObjetId;
              div.className = slotName;

              Object.entries(object).forEach(([propertyName, property]) => {
                const queryResult = templateContent.querySelectorAll('[name=' + propertyName + ']');
                console.log(queryResult);
                if(queryResult !== null) {
                  const element = queryResult.item(0);
                  console.log(element);
                  element.setAttribute('content', property.value);
                  div.appendChild(element)
                }


              });

              elementContainer.appendChild(div);

              idList.push(slotObjetId);
            });

          parentElement.appendChild(elementContainer);

          await this.#applySlotDataChanged(slotDataChanged(
            elementContainerId, idList
          ))
          return;
        }
      }
    }
  }

  /**
   * @param {SlotDataChanged} payload
   * @return {Promise<void>}
   */
  async #applySlotDataChanged(payload) {
    this.#publish(payload.elementContainerId + "/" + slotDataChanged.name, payload)
  }


  async #loadTemplate(templateId) {
    console.log(templateId);
    if (this.#shadowRoot.getElementById(templateId)) {
      return;
    }
    const template = await this.#template(templateId)
    this.#shadowRoot.appendChild(template);
  }

}