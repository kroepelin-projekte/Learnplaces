import artefact from './Artefact.mjs';

const domainMessage = {};
domainMessage.defined = 'defined';
domainMessage.created = 'created';
/**
 * @param {{id: string, tagName: string}} payload
 * @return {Object}
 */
domainMessage.newCreated = (payload) => {
  return artefact.newMessage(domainMessage.created, payload)
}
export default domainMessage;