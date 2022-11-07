import dataFetcherType from './DataFetcherTypes/DataFetcherType.mjs';

const artefact = {};

/**
 * @return {FluxDataFetcherType}
 */
artefact.newDataFetcherType = function(tagName) {
  return dataFetcherType[tagName]
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
