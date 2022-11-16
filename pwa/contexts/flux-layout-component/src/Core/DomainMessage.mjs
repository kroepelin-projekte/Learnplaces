/** @typedef {{ headers: {context: string, id: string, messageName: string}, payload: Object }} DomainMessage */

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
  static defined(payload ) {
    return payload
  }

  /**
   * @param {{name: string, id: string}} payload
   * @return {{name: string, id: string}} payload
   */
  static created( payload ) {
    return payload
  }
}