import domainMessage from './DomainMessage.mjs';
import artefact from './Artefact.mjs';

export default class Aggregate  {

  /**
   * @var {string}
   */
  tagName;
  /**
   * @var {function}
   */
  #publish;


  static create(tagName, publishDomainEventCallback) {
    const obj = new this(tagName, publishDomainEventCallback);
    obj.#initialize();
    obj.#publish(domainMessage.newCreated({id: tagName,tagName: tagName}))
    return obj;
  }


  constructor(tagName, publishDomainEventCallback) {
    this.tagName = tagName;
    this.#publish = publishDomainEventCallback;
  }


  #initialize() {
    const templateId = "flux-projection-template";

    document.body.insertAdjacentHTML('afterbegin',
      '<template id="'+templateId+'">' +
      '<slot name="projection-type">' +
      '   <data slot="projection-type" value=""></data>\n' +
      '</slot>' +
      '</template>'
      );

    customElements.define(
      this.tagName,
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

  /*
  #elementId;

  #projectionApi = "http://127.3.3.3/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/api.php";
  #domainEventPublisher

  async projectData(
    projectionName,
  ) {
    const data = {};

    const response = await (
      await fetch(
        this.#projectionApi + "project" + projectionName
      )
    );
    if (response.ok) {
      const json = await response.json();
      this.#applyDataProjected(
        {
          "elementId": this.#elementId,
          "data": json.data
        }
      )
    } else {
      alert("HTTP-Error: " + response.status);
    }
  }

  #applyDataProjected(event) {

    this.#domainEventPublisher.publish(
      dataProjected.projectionName + 'Projected',
      dataProjected
    )

  }
*/

}