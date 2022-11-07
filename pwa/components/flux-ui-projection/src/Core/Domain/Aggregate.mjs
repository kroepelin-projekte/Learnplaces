import domainMessage from './DomainMessage.mjs';
import artefact from './Artefact.mjs';
import projectionType from './ProjectionTypes/ProjectionType.mjs';

export default class Aggregate  {

  /**
   * @var {FluxProjectionType}
   */
  #fluxProjectionType;
  /**
   * @var {function}
   */
  #publish;


  static create(tagName, publishDomainEventCallback) {
    const obj = new this(tagName, publishDomainEventCallback);
    obj.#initialize();
    obj.#publish(domainMessage.newCreated(obj.#fluxProjectionType))
    return obj;
  }


  constructor(tagName, publishDomainEventCallback) {
    this.#fluxProjectionType = artefact.newProjectionType(tagName)
    this.#publish = publishDomainEventCallback;
  }


  #initialize() {
    document.body.insertAdjacentHTML('afterbegin', this.#fluxProjectionType.template);

    const templateId = this.#fluxProjectionType.templateId;
    customElements.define(
      this.#fluxProjectionType.tagName,
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