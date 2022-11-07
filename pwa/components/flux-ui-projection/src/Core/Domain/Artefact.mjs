const artefact = {};

/**
 * @typedef {{react: string, publish: string, templateId: string, type: string }} FluxProjectionType
 */

/**
 * @return {FluxProjectionType}
 */
artefact.newProjectionType = function(payload) {
  return payload
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
