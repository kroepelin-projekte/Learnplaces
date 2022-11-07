import artefact from './Artefact.mjs';

const domainMessage = {};
domainMessage.created = 'created';
/**
 * @param {{id: string, tagName: string}} payload
 * @return {Object}
 */
domainMessage.newCreated = (payload) => {
  return artefact.newMessage(domainMessage.created, payload)
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