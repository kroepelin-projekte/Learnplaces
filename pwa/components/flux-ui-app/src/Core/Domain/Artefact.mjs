import domainMessage from './DomainMessage.mjs';
import appElementArtefact from './AppElementArtefact/AppElement.mjs';

const artefact = {};

artefact.fluxAppElementArtefact = appElementArtefact;

/**
 * @return {{id: string, tagName: string, templateId: string, template: string}}
 */
artefact.newFluxAppElement = () => {
  return appElementArtefact.appElement
}

/**
 * @param {string} name
 * @param {JSON} data
 * @return {{name, value}}
 */
artefact.newFluxSlotElement = function (name, data) {
  return new class FluxSlotElement {
    /** @var string */
    name;
    /** @var Object */
    data;

    constructor() {
      this.name = name;
      this.data = JSON.parse(data);
    }
  }
}

/**
 * @param {string} tagName
 * @param {string} src
 * @return {{id, src}}
 */
artefact.newFluxComponentApi = function (tagName, src) {
  return new class FluxComponentApi {
    /** @var string */
    tagName;
    /** @var string */
    src;

    constructor() {
      this.tagName = tagName;
      this.src = src;
    }
  }
}

/**
 * @param {string} id
 * @param {string} src
 * @return {{id, src}}
 */
artefact.newFluxComponentElement = function (id, src) {
  return new class FluxComponentElement {
    /** @var Object */
    id;
    /** @var string */
    src;

    constructor() {
      this.id = id;
      this.src = src;
    }
  }
}

/**
 * @param {string} name
 * @param {object} payload
 * @return {{headers, payload}}
 */
artefact.newMessage = function (name, payload) {
  return {
    "headers": {
      "name": name
    },
    payload
  }
}

export default artefact;
