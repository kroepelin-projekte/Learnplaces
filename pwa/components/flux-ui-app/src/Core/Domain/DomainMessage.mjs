import artefact from './Artefact.mjs';

const domainMessage = {};

domainMessage.created = 'created';
/**
 * @param {{id: string, tagName: string}} payload
 * @return {Object}
 */
domainMessage.newCreated = function (payload) {
  return artefact.newMessage(domainMessage.created, payload)
}


/**
 * @param {FluxHtmlElement} payload
 * @return {FluxHtmlElement}
 */
domainMessage.initalized = 'initalized';
domainMessage.newInitalized = function (payload) {
  return artefact.newMessage(domainMessage.initalized, payload)
}


/**
 * @param {FluxSlotElement} payload
 * @return {Object}
 */
domainMessage.slotchanged = 'slotchanged';
domainMessage.newSlotChanged = (payload) => {
  return artefact.newMessage(domainMessage.slotchanged, payload)
}



export default domainMessage;