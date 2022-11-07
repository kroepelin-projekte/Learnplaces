import layoutItem from './LayoutItems/LayoutItem.mjs';

const artefact = {};

/**
 * @typedef {{id: string, tagName: string, templateId: string, template: string}} FluxLayoutItem
 */
artefact.newFluxLayoutItem = function(tagName) {
  return layoutItem[tagName]
}

/**
 * @param {string} name
 * @param {object} payload
 * @return {{headers, payload}}
 */
artefact.newMessage = function(name, payload) {
  return new class Message {
    /** @var Object */
    headers;
    /** @var string */
    payload;

    constructor() {
      this.headers = {
        "name": name
      }
      this.payload = payload;
    }
  }
}

export default artefact;
