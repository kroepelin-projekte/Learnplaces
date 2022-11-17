/** @typedef {{ name: string, payload: Object }} DomainMessage */
/** @typedef {{name: string, address: string}} ElementCreatedPayload */
/** @typedef {{parentName: string, slotName: string, name: string, address: string}} SlotElementCreatedPayload */
/** @typedef {{parentName: string, slotName: string, name: string, data: {id: string, value: string}}} ContentCreatedPayload */

export default class DomainMessage {
  /**
   * @return {DomainMessage}
   */
  static initialized() {
    return {
      name: "initialized",
      payload: {}
    }
  }

  /**
   * @param {{name: string}} payload
   * @return {{name: string}} payload
   */
  static defined(payload) {
    return payload
  }


  static elementCreated = "elementCreated";
  static slotElementCreated = "slotElementCreated";
  static contentCreated = "contentCreated";
}