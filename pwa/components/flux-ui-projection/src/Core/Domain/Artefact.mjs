import projectionType from './ProjectionTypes/ProjectionType.mjs';

const artefact = {};

/**
 * @return {FluxProjectionType}
 */
artefact.newProjectionType = function(tagName) {
  return projectionType[tagName]
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
